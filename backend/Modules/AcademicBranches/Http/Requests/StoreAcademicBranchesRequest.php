<?php

namespace Modules\AcademicBranches\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicBranchesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // عدلها لاحقًا حسب نظام الصلاحيات
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:academic_branches,name',
            'description' => 'nullable|string',
        ];
    }

    /**
     * رسائل الأخطاء المخصصة.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفرع الأكاديمي مطلوب.',
            'name.string'   => 'اسم الفرع الأكاديمي يجب أن يكون نصاً.',
            'name.max'      => 'اسم الفرع الأكاديمي يجب ألا يتجاوز 255 حرفاً.',
            'name.unique'   => 'اسم الفرع الأكاديمي مستخدم من قبل.',

            'description.string' => 'الوصف يجب أن يكون نصاً.',
        ];
    }
}