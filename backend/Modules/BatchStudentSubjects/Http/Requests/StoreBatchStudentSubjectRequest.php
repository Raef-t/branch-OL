<?php

namespace Modules\BatchStudentSubjects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchStudentSubjectRequest extends FormRequest
{
    /**
     * تحديد صلاحية تنفيذ الطلب
     */
    public function authorize(): bool
    {
        // يمكن ربطه لاحقًا بـ Policy
        return true;
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        return [
            'batch_student_id' => [
                'required',
                'exists:batch_student,id',
            ],

            'batch_subject_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'batch_subject_ids.*' => [
                'required',
                'exists:batch_subjects,id',
            ],

            'status' => [
                'sometimes',
                'string',
                'in:active,dropped,completed',
            ],
        ];
    }

    /**
     * رسائل الخطأ المخصصة
     */
    public function messages(): array
    {
        return [
            'batch_student_id.required' => 'يجب تحديد تسجيل الطالب في الدفعة',
            'batch_student_id.exists'   => 'تسجيل الطالب في الدفعة غير موجود',

            'batch_subject_ids.required' => 'يجب تحديد مادة واحدة على الأقل',
            'batch_subject_ids.array'    => 'قائمة المواد يجب أن تكون مصفوفة',
            'batch_subject_ids.min'      => 'يجب تحديد مادة واحدة على الأقل',

            'batch_subject_ids.*.exists' => 'إحدى المواد المحددة غير موجودة',

            'status.in' => 'قيمة الحالة يجب أن تكون: active أو dropped أو completed',
        ];
    }
}
