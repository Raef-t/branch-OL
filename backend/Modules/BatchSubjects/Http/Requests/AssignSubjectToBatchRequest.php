<?php

namespace Modules\BatchSubjects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignSubjectToBatchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'batch_id' => 'sometimes|required|integer|exists:batches,id',
            'subject_id' => 'sometimes|required|integer|exists:subjects,id',
            'instructor_subject_id' => 'sometimes|nullable|integer|exists:instructor_subjects,id',
            'weekly_lessons' => 'sometimes|required|integer|min:1',
            'notes' => 'sometimes|nullable|string|max:500',
            'allow_same_subject_same_day' => 'sometimes|nullable|boolean',
            'max_lessons_per_day' => 'sometimes|nullable|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'batch_id.required' => 'معرف الدورة مطلوب',
            'batch_id.exists' => 'الدورة غير موجودة',
            'instructor_subject_id.required' => 'معرف ربط المدرس بالمادة مطلوب',
            'instructor_subject_id.exists' => 'الربط غير موجود',
            'weekly_lessons.required' => 'عدد الحصص الأسبوعية مطلوب',
            'weekly_lessons.integer' => 'يجب أن يكون عدد الحصص رقماً صحيحاً',
            'weekly_lessons.min' => 'يجب أن يكون عدد الحصص حصة واحدة على الأقل',
            'notes.max' => 'الملاحظات لا تتجاوز 500 حرف',
        ];
    }
}