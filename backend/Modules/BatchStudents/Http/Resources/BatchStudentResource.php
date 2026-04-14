<?php

namespace Modules\BatchStudents\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchStudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hasCustomSubjects =
            $this->relationLoaded('batchSubjects')
            && $this->batchSubjects->isNotEmpty();

        return [
            'id' => $this->id,

            // ✅ ابقِ الـ IDs (مفيد برمجياً)
            'student_id' => $this->student_id,
            'batch_id'   => $this->batch_id,

            // ✅ معلومات واضحة للعرض
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id'        => $this->student->id,
                    'full_name' => $this->student->full_name
                        ?? trim(($this->student->first_name ?? '').' '.($this->student->last_name ?? ''))
                        ?: null,
                    'name'      => $this->student->full_name, // keep name for backward compatibility
                ];
            }),

            'batch' => $this->whenLoaded('batch', function () {
                $batchData = [
                    'id'   => $this->batch->id,
                    'name' => $this->batch->name ?? null,
                ];

                if ($this->batch->relationLoaded('batchSubjects')) {
                    $batchData['batch_subjects'] = $this->batch->batchSubjects->map(function ($bs) {
                        return [
                            'id' => $bs->id,
                            'subject' => $bs->subject ? [
                                'id' => $bs->subject->id,
                                'name' => $bs->subject->name,
                            ] : null,
                        ];
                    });
                }

                return $batchData;
            }),

            'enrollment_type' => $hasCustomSubjects ? 'partial' : 'full',

            'enrollment_description' => $hasCustomSubjects
                ? 'الطالب مسجّل ببعض مواد الدفعة فقط'
                : 'الطالب مسجّل بالدفعة كاملة',

            'has_custom_subjects' => $hasCustomSubjects,

            'subjects' => $hasCustomSubjects
                ? $this->batchSubjects->map(function ($item) {
                    return [
                        'batch_subject_id' => $item->batch_subject_id,
                        'subject' => $item->batchSubject && $item->batchSubject->subject
                            ? [
                                'id'   => $item->batchSubject->subject->id,
                                'name' => $item->batchSubject->subject->name,
                            ]
                            : null,
                        'status' => $item->status,
                    ];
                })
                : null,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
