<?php

namespace Modules\Exams\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'batch_subject_id' => $this->batch_subject_id,
            'name' => $this->name,
            'exam_date' => $this->exam_date,
            'exam_time' => $this->exam_time,
            'total_marks' => $this->total_marks,
            'exam_end_time' => $this->exam_end_time,
            'passing_marks' => $this->passing_marks,
            'status' => $this->status,
            'exam_type' => $this->examType ? [
                'id' => $this->examType->id,
                'name' => $this->examType->name,
                'description' => $this->examType->description,
            ] : null,
            'remarks' => $this->remarks,
            'batch_subject' => $this->batchSubject ? [
                'id' => $this->batchSubject->id,
                'batch' => $this->batchSubject->batch ? [
                    'id' => $this->batchSubject->batch->id,
                    'name' => $this->batchSubject->batch->name,
                ] : null,
                'class_room' => $this->batchSubject->classRoom ? [
                    'id' => $this->batchSubject->classRoom->id,
                    'name' => $this->batchSubject->classRoom->name,
                    'code' => $this->batchSubject->classRoom->code,
                ] : null,
                'subject' => $this->batchSubject->subject ? [
                    'id' => $this->batchSubject->subject->id,
                    'name' => $this->batchSubject->subject->name,
                ] : null,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}