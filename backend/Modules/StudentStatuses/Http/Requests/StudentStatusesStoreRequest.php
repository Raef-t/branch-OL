<?php

namespace Modules\StudentStatuses\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentStatusesStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:50',
            'code'        => 'required|string|max:20|unique:student_statuses,code',
            'description' => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الحالة مطلوب.',
            'name.string'   => 'اسم الحالة يجب أن يكون نصاً.',
            'name.max'      => 'اسم الحالة يجب ألا يتجاوز 50 حرفاً.',

            'code.required' => 'كود الحالة مطلوب.',
            'code.string'   => 'كود الحالة يجب أن يكون نصاً.',
            'code.max'      => 'كود الحالة يجب ألا يتجاوز 20 حرفاً.',
            'code.unique'   => 'كود الحالة مستخدم من قبل.',

            'description.string' => 'الوصف يجب أن يكون نصاً.',

            'is_active.boolean' => 'حقل التفعيل يجب أن يكون صحيح أو خطأ (true/false).',
        ];
    }
}