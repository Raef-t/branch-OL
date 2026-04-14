<?php

namespace Modules\BatchStudents\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class StudentWithRemainingAmountResource extends JsonResource
{
    public function toArray($request)
    {
        $student  = $this->student;
        $contract = $student?->latestActiveEnrollmentContract;

        $primaryGuardian = $student?->family?->guardians
            ?->firstWhere('is_primary_contact', true);

        $primaryPhone = $primaryGuardian?->primaryPhone?->phone_number ?? null;

        // // التحقق من حضور الطالب اليوم
        // $attendedToday = $student
        //     ? $student->attendances()
        //     ->whereDate('attendance_date', now()->toDateString())
        //     ->exists()
        //     : false;

        return [
            'id'                   => $student?->id,
            'user_id'              => $student?->user_id,
            'first_name'           => $student?->first_name,
            'last_name'            => $student?->last_name,
            'full_name'            => $student?->full_name,
            'gender'               => $student?->gender,
            'profile_photo_url'    => $student?->profile_photo_url,

            'primary_phone'        => $primaryPhone,

            'attendance_enrolment' => $this->created_at
                ? Carbon::parse($this->created_at)->toDateString()
                : null,

            'remaining_amount_usd' => $contract
                ? max(
                    $contract->final_amount_usd - $contract->paid_amount_usd,
                    0
                )
                : null,

            'batch_student_id'     => $this->id,
            'is_partial'           => (bool) $this->is_partial,
            'subjects_count'       => (bool) $this->is_partial 
                ? $this->batchSubjects()->count() 
                : $this->batch->batchSubjects()->count(),

            // الحقل الجديد
            'attended_today' => $student?->attendances->isNotEmpty(),
        ];
    }
}
