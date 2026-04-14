<?php

namespace Modules\Students\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="StudentDetailedResource",
 *     title="Student Detailed Resource",
 *     description="جميع بيانات الطالب مع العلاقات الكاملة",
 *
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="user_id", type="integer", example=123, description="معرف المستخدم المرتبط بالطالب"),
 *     @OA\Property(property="full_name", type="string", example="أحمد محمد"),
 *     @OA\Property(property="gender", type="string", example="male"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", example="2012-05-10"),
 *     @OA\Property(property="enrollment_date", type="string", format="date", example="2023-09-01"),
 *     @OA\Property(property="start_attendance_date", type="string", format="date", example="2023-09-05"),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *
 *     @OA\Property(property="health_status", type="string", example="جيد"),
 *     @OA\Property(property="psychological_status", type="string", example="مستقر"),
 *
 *     @OA\Property(
 *         property="branch",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="فرع دمشق")
 *     ),
 *
 *     @OA\Property(
 *         property="institute_branch",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="معهد المركز")
 *     ),
 *
 *     @OA\Property(
 *         property="status",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=3),
 *         @OA\Property(property="name", type="string", example="منتظم")
 *     ),
 *
 *     @OA\Property(
 *         property="city",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=5),
 *         @OA\Property(property="name", type="string", example="دمشق")
 *     ),
 *
 *     @OA\Property(
 *         property="bus",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="باص 1"),
 *         @OA\Property(property="driver_name", type="string", example="أبو عمر"),
 *         @OA\Property(property="plate_number", type="string", example="123456")
 *     ),
 *
 *     @OA\Property(
 *         property="family",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=14),
 *         @OA\Property(property="user_id", type="integer", example=22),
 *         @OA\Property(
 *             property="guardians",
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=7),
 *                 @OA\Property(property="first_name", type="string", example="خالد"),
 *                 @OA\Property(property="last_name", type="string", example="المهاجر"),
 *                 @OA\Property(property="relationship", type="string", example="Father"),
 *                 @OA\Property(property="national_id", type="string", example="00998877"),
 *                 @OA\Property(property="phone", type="string", example="0999999999"),
 *                 @OA\Property(property="is_primary_contact", type="boolean", example=true),
 *
 *                 @OA\Property(
 *                     property="contact_details",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=5),
 *                         @OA\Property(property="type", type="string", example="whatsapp"),
 *                         @OA\Property(property="full_phone_number", type="string", example="+963987654321"),
 *                         @OA\Property(property="value", type="string", example="987654321"),
 *                         @OA\Property(property="is_primary", type="boolean", example=true)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Property(
 *         property="batch",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=4),
 *         @OA\Property(property="name", type="string", example="الشعبة A"),
 *         @OA\Property(property="start_date", type="string", example="2023-09-01"),
 *         @OA\Property(property="end_date", type="string", example="2024-06-01")
 *     ),
 *
 *     @OA\Property(
 *         property="enrollment_contract",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=33),
 *         @OA\Property(property="discount_percentage", type="number", example=25),
 *         @OA\Property(property="discount_amount", type="number", example=100.00),
 *         @OA\Property(property="total_amount_usd", type="number", example=400),
 *         @OA\Property(property="final_amount_usd", type="number", example=300)
 *     ),
 *
 *     @OA\Property(property="profile_photo_url", type="string", example="https://domain.com/photo.jpg"),
 *     @OA\Property(property="id_card_photo_url", type="string", example="https://domain.com/card.jpg"),
 *
 *     @OA\Property(property="created_at", type="string", example="2025-01-10 12:30:00"),
 *     @OA\Property(property="updated_at", type="string", example="2025-01-10 12:30:00")
 * )
 */

class StudentDetailedResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => "{$this->first_name} {$this->last_name}",
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'birth_place' => $this->birth_place,
            'national_id' => $this->national_id,
            'school_id' => $this->school_id,
            'enrollment_date' => $this->enrollment_date?->format('Y-m-d'),
            'start_attendance_date' => $this->start_attendance_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'how_know_institute' => $this->how_know_institute,
            'health_status' => $this->health_status,
            'psychological_status' => $this->psychological_status,

            // جلب كافة الأرقام المرتبطة بالطالب أو عائلته أو أوصيائه
            'contact_details' => $allContacts = \Modules\ContactDetails\Models\ContactDetail::where(function ($q) {
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
                ->values(),

            'contacts' => \Modules\ContactDetails\Http\Resources\ContactDetailResource::collection($allContacts),
            'personal_contacts' => \Modules\ContactDetails\Http\Resources\ContactDetailResource::collection($allContacts->where('owner_type', 'student')),

            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
            ] : null,

            'institute_branch' => $this->instituteBranch ? [
                'id' => $this->instituteBranch->id,
                'name' => $this->instituteBranch->name,
            ] : null,

            'status' => $this->status ? [
                'id' => $this->status->id,
                'name' => $this->status->name,
            ] : null,

            'city' => $this->city ? [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ] : null,

            'bus' => $this->bus ? [
                'id' => $this->bus->id,
                'name' => $this->bus->name,
                'driver_name' => $this->bus->driver_name,
                'plate_number' => $this->bus->plate_number,
            ] : null,

            'family' => $this->family ? [
                'id' => $this->family->id,
                'user_id' => $this->family->user_id,
                'family_contacts' => \Modules\ContactDetails\Http\Resources\ContactDetailResource::collection($this->family->contactDetails),
                'guardians' => $this->family->guardians->map(function ($guardian) {
                    return [
                        'id' => $guardian->id,
                        'first_name' => $guardian->first_name,
                        'last_name' => $guardian->last_name,
                        'name' => trim("{$guardian->first_name} {$guardian->last_name}"),
                        'relationship' => strtolower($guardian->relationship),
                        'national_id' => $guardian->national_id,
                        'legacy_phone' => $guardian->phone,
                        'is_primary_contact' => (bool) $guardian->is_primary_contact,

                        'contact_details' => \Modules\ContactDetails\Http\Resources\ContactDetailResource::collection($guardian->contactDetails),
                    ];
                }),
            ] : null,

            'academic_records' => $this->academicRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'total_score' => $record->total_score,
                    'year' => $record->year,
                    'description' => $record->description,
                    'created_at' => $record->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $record->updated_at?->format('Y-m-d H:i:s'),
                ];
            }),

            'batch' => $this->latestBatchStudent && $this->latestBatchStudent->batch ? [
                'id' => $this->latestBatchStudent->batch->id,
                'name' => $this->latestBatchStudent->batch->name,
                'start_date' => $this->latestBatchStudent->batch->start_date,
                'end_date' => $this->latestBatchStudent->batch->end_date,
                'batch_subjects' => $this->latestBatchStudent->batch->batchSubjects->map(function ($bs) {
                    return [
                        'id' => $bs->id,
                        'subject' => $bs->subject ? [
                            'id' => $bs->subject->id,
                            'name' => $bs->subject->name,
                        ] : null,
                    ];
                }),
            ] : null,

            'school' => $this->school ? [
                'id' => $this->school->id,
                'name' => $this->school->name,
            ] : null,

            'enrollment_contract' => $this->latestActiveEnrollmentContract ? [
                'id' => $this->latestActiveEnrollmentContract->id,
                'discount_percentage' => $this->latestActiveEnrollmentContract->discount_percentage,
                'discount_amount' => $this->latestActiveEnrollmentContract->discount_amount,
                'total_amount_usd' => $this->latestActiveEnrollmentContract->total_amount_usd,
                'final_amount_usd' => $this->latestActiveEnrollmentContract->final_amount_usd,
            ] : null,

            'profile_photo_url' => $this->profile_photo_url,
            'id_card_photo_url' => $this->id_card_photo_url,

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
