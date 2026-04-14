<?php
namespace Modules\BatchStudentSubjects\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class BatchStudentSubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'batch_student_id' => $this->batch_student_id,
            'batch_subject_id' => $this->batch_subject_id,

            'status' => $this->status,

            'batch_student' => $this->whenLoaded('batchStudent', function () {
                return [
                    'id' => $this->batchStudent->id,

                    'student_id' => $this->batchStudent->student_id,
                    'batch_id'   => $this->batchStudent->batch_id,
                ];
            }),

            'batch_subject' => $this->whenLoaded('batchSubject', function () {
                return [
                    'id' => $this->batchSubject->id,
                    'batch_id' => $this->batchSubject->batch_id,
                ];
            }),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
