<?php

namespace Modules\Students\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{

    public function toArray($request)
    {
        // تحديد ما إذا حضر الطالب اليوم
        $todayAttendance = $this->attendances()
            ->whereDate('attendance_date', now()->format('Y-m-d'))
            ->first();

        return [
            'id'                    => $this->id,
            'institute_branch_id'   => $this->institute_branch_id,
            'family_id'             => $this->family_id,
            'user_id'               => $this->user_id,
            'first_name'            => $this->first_name,
            'last_name'             => $this->last_name,
            'school_id'             => $this->school_id,
            'full_name'             => $this->first_name . ' ' . $this->last_name,
            'date_of_birth'         => $this->date_of_birth?->format('Y-m-d'),
            'birth_place'           => $this->birth_place,
            'profile_photo_url'     => $this->profile_photo_url,
            'id_card_photo_url'     => $this->id_card_photo_url,
            'branch_id'             => $this->branch_id,
            'enrollment_date'       => $this->enrollment_date?->format('Y-m-d'),
            'start_attendance_date' => $this->start_attendance_date?->format('Y-m-d'),
            'gender'                => $this->gender,
            'previous_school_name'  => $this->previous_school_name,
            'national_id'           => $this->national_id,
            'how_know_institute'    => $this->how_know_institute,
            'bus_id'                => $this->bus_id,
            'notes'                 => $this->notes,

            'health_status'         => $this->health_status,
            'psychological_status'  => $this->psychological_status,

            'status_id'             => $this->status_id,
            'city_id'               => $this->city_id,
            'qr_code_data'          => $this->qr_code_data,
            'created_at'            => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'            => $this->updated_at?->format('Y-m-d H:i:s'),

            // معلومات الدفعة الأخيرة
            'batch' => $this->latestBatchStudent && $this->latestBatchStudent->batch
                ? [
                    'id'         => $this->latestBatchStudent->batch->id,
                    'name'       => $this->latestBatchStudent->batch->name ?? null,
                    'start_date' => $this->latestBatchStudent->batch->start_date ?? null,
                    'end_date'   => $this->latestBatchStudent->batch->end_date ?? null,
                ]
                : null,

            // قائمة الأوصياء
            'guardians' => $this->family && $this->family->relationLoaded('guardians')
                ? $this->family->guardians->map(function ($guardian) {
                    return [
                        'id'                 => $guardian->id,
                        'first_name'         => $guardian->first_name,
                        'last_name'          => $guardian->last_name,
                        'national_id'        => $guardian->national_id,
                        'relationship'       => $guardian->relationship,
                        'phone'              => $guardian->phone,
                        'is_primary_contact' => (bool) $guardian->is_primary_contact,
                        'contact_details'    => \Modules\ContactDetails\Http\Resources\ContactDetailResource::collection($guardian->contactDetails ?? []),
                    ];
                })
                : null,

            // الحقل الجديد: إذا حضر الطالب اليوم
            'attended_today' => $todayAttendance ? true : false,

            // المدرسة
            'school' => $this->school ? [
                'id'   => $this->school->id,
                'name' => $this->school->name,
            ] : null,

            // بيانات الاتصال الموحدة (جلب كافة الأرقام المرتبطة بالطالب أو عائلته أو أوصيائه)
            'contact_details' => $contactCollection = \Modules\ContactDetails\Http\Resources\ContactDetailResource::collection(
                \Modules\ContactDetails\Models\ContactDetail::where(function($q) {
                    $q->where('student_id', $this->id);
                    if ($this->family_id) {
                        $q->orWhere('family_id', $this->family_id);
                        $guardianIds = $this->family ? $this->family->guardians->pluck('id')->toArray() : [];
                        if (!empty($guardianIds)) {
                            $q->orWhereIn('guardian_id', $guardianIds);
                        }
                    }
                })
                ->orderBy('is_primary', 'desc')
                ->get()
                ->unique('id')
                ->values()
            ),
            'contacts' => $contactCollection,
        ];
    }
}
