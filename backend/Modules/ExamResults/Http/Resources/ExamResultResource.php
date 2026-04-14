<?php

namespace Modules\ExamResults\Http\Resources;
use OpenApi\Annotations as OA;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="ExamResultResource",
 *     type="object",
 *     title="ExamResult Resource",
 *     description="شكل نتيجة الامتحان",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="exam_id", type="integer", example=10),
 *     @OA\Property(property="student_id", type="integer", example=55),
 *     @OA\Property(property="obtained_marks", type="number", format="float", example=87.5),
 *     @OA\Property(property="is_passed", type="boolean", example=true),
 *     @OA\Property(property="remarks", type="string", example="أداء ممتاز"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-05T12:27:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-06T08:15:00Z")
 * )
 */

class ExamResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'exam_id' => $this->exam_id,
            'student_id' => $this->student_id,
            'obtained_marks' => $this->obtained_marks,
            'is_passed' => $this->is_passed,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}