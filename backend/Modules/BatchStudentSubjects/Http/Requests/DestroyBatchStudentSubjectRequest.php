<?php

namespace Modules\BatchStudentSubjects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\BatchStudentSubjects\Models\BatchStudentSubject;

class DestroyBatchStudentSubjectRequest extends FormRequest
{
    protected ?BatchStudentSubject $record = null;

    public function authorize(): bool
    {
        // لاحقًا يمكن ربطها بـ Policy
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    /**
     * تحميل السجل قبل الوصول للكونترولر
     */
    protected function prepareForValidation(): void
    {
        $this->record = BatchStudentSubject::find(
            $this->route('id')
        );
    }

    /**
     * التحقق المنطقي
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (! $this->record) {
                $validator->errors()->add(
                    'id',
                    'سجل المادة غير موجود أو تم حذفه مسبقًا'
                );
            }

        });
    }

    /**
     * إتاحة السجل للكونترولر
     */
    public function record(): BatchStudentSubject
    {
        return $this->record;
    }
}
