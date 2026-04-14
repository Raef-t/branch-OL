<?php

namespace Modules\BatchStudents\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchStudentSubjectsRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخوّل بتنفيذ الطلب
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        return [
            'batch_subject_ids'   => ['required', 'array', 'min:1'],
            'batch_subject_ids.*' => ['integer', 'distinct'],
        ];
    }

    /**
     * رسائل التحقق المخصصة (عربية وواضحة)
     */
    public function messages(): array
    {
        return [
            'batch_subject_ids.required' => 'يجب تحديد مادة واحدة على الأقل',
            'batch_subject_ids.array'    => 'صيغة المواد غير صحيحة',
            'batch_subject_ids.min'      => 'يجب تحديد مادة واحدة على الأقل',

            'batch_subject_ids.*.integer'  => 'معرّف المادة غير صالح',
            'batch_subject_ids.*.distinct' => 'لا يمكن تكرار نفس المادة أكثر من مرة',
        ];
    }

    /**
     * أسماء الحقول (لتحسين رسائل الأخطاء)
     */
    public function attributes(): array
    {
        return [
            'batch_subject_ids' => 'مواد الدفعة',
        ];
    }

    /**
     * تهيئة البيانات قبل التحقق (اختياري لكن مفيد)
     * يضمن أن القيم أرقام فعلًا
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('batch_subject_ids') && is_array($this->batch_subject_ids)) {
            $this->merge([
                'batch_subject_ids' => array_map('intval', $this->batch_subject_ids),
            ]);
        }
    }
}
