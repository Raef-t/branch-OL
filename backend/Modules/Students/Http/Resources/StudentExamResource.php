<?php

namespace Modules\Students\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'exam_id'       => $this->id,
            'subject_name'  => $this->batchSubject?->subject?->name ?? __('غير محدد'),
            'exam_date'     => $this->exam_date?->format('Y-m-d'),
            'exam_time'     => $this->exam_time,
            'exam_type'     => $this->examType?->name ?? __('غير محدد'),
            'total_marks'   => $this->total_marks,
            'passing_marks' => $this->passing_marks,

            // معلومات الدفعة
            'batch_name'    => $this->batchSubject?->batch?->name ?? __('غير محدد'),

            // معلومات القاعة
            'class_section' => $this->batchSubject?->batch?->classRoom?->name ?? __('غير محدد'),
            'room_number'   => $this->batchSubject?->batch?->classRoom?->code ?? __('غير محدد'),
        ];
    }
}
