<?php

namespace Modules\StudentExits\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="StudentExitLogResource",
 *     title="Student Exit Log Resource",
 *     description="تمثيل بيانات خروج الطالب",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="student_id", type="integer", example=15),
 *     @OA\Property(property="exit_date", type="string", format="date", example="2025-01-14"),
 *     @OA\Property(property="exit_time", type="string", example="13:40"),
 *     @OA\Property(property="return_time", type="string", nullable=true, example="14:10"),
 *     @OA\Property(property="exit_type", type="string", nullable=true, example="medical"),
 *     @OA\Property(property="reason", type="string", nullable=true, example="حالة صحية"),
 *     @OA\Property(property="note", type="string", nullable=true, example="خرج مع ولي الأمر"),
 *     @OA\Property(property="recorded_by", type="integer", example=7),
 *
 *     @OA\Property(
 *         property="student",
 *         type="object",
 *         nullable=true,
 *         description="بيانات مختصرة عن الطالب",
 *         @OA\Property(property="id", type="integer", example=15),
 *         @OA\Property(property="name", type="string", example="أحمد محمد")
 *     )
 * )
 */
class StudentExitLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'student_id'  => $this->student_id,
            'exit_date'   => optional($this->exit_date)->toDateString(),
            'exit_time'   => $this->exit_time ? $this->exit_time->format('H:i:s') : null,
            'return_time' => $this->return_time ? $this->return_time->format('H:i:s') : null,
            'exit_type'   => $this->exit_type,
            'reason'      => $this->reason,
            'note'        => $this->note,
            'recorded_by' => $this->recorded_by,

            'student' => $this->whenLoaded('student', function () {
                return [
                    'id'   => $this->student->id,
                    'name' => $this->student->user->name ?? $this->student->name ?? null,
                ];
            }),
        ];
    }
}
