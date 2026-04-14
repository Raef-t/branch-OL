<?php

namespace Modules\BatchStudents\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnassignedStudentResource extends JsonResource
{
    /**
     * Resource لعرض بيانات طالب غير مرتبط بشعبة.
     * يتضمن حقل assignment_status لتوضيح حالة الطالب.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'full_name'         => $this->full_name,
            'gender'            => $this->gender,
            'profile_photo_url' => $this->profile_photo_url,
            'enrollment_date'   => $this->enrollment_date?->format('Y-m-d'),

            // الموقع الجغرافي
            'institute_branch' => $this->whenLoaded('instituteBranch', function () {
                return $this->instituteBranch ? [
                    'id'   => $this->instituteBranch->id,
                    'name' => $this->instituteBranch->name,
                ] : null;
            }),

            // الفرع الأكاديمي
            'academic_branch' => $this->whenLoaded('branch', function () {
                return $this->branch ? [
                    'id'   => $this->branch->id,
                    'name' => $this->branch->name,
                ] : null;
            }),

            // حالة الطالب بالنسبة للشعبة المستهدفة
            'assignment_status'             => $this->assignment_status ?? null,
            'assignment_status_description' => $this->getStatusDescription(),
        ];
    }

    /**
     * وصف عربي لحالة التوافق.
     */
    private function getStatusDescription(): string
    {
        return match ($this->assignment_status) {
            'matching'              => 'يطابق الفرع الأكاديمي والموقع الجغرافي للشعبة',
            'no_location'           => 'لا يملك موقع جغرافي — سيتم ربطه بموقع الشعبة عند الإضافة',
            'no_branch'             => 'لا يملك فرع أكاديمي محدد',
            'no_branch_no_location' => 'لا يملك فرع أكاديمي ولا موقع جغرافي',
            default                 => 'غير محدد',
        };
    }
}
