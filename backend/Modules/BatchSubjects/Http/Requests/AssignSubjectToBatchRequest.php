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
            'batch_id' => 'required|integer|exists:batches,id',
            'instructor_subject_id' => 'required|integer|exists:instructor_subjects,id',
            'weekly_lessons' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
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