<?php

namespace Modules\InstituteBranches\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstituteBranchesUpdateRequest extends FormRequest
{
    /**
     * هل المستخدم مخول بتنفيذ هذا الطلب؟
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق للمدخلات.
     */
    public function rules(): array
    {
        $branchId = $this->route('id');
        $branchExists = \Modules\InstituteBranches\Models\InstituteBranch::find($branchId);

        $uniqueCodeRule = $branchExists
            ? 'unique:institute_branches,code,' . $branchId
            : 'nullable';

        return [
            'name'         => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'code'         => 'required|string|max:50|' . $uniqueCodeRule,
            'country_code' => 'nullable|string|max:5',
             'phone'        => 'nullable|regex:/^[0-9]{7,15}$/',
            'email'        => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active'    => 'sometimes|boolean',
        ];
    }

    /**
     * رسائل الأخطاء المخصصة.
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'اسم الفرع مطلوب.',
            'name.string'        => 'اسم الفرع يجب أن يكون نصاً.',
            'name.max'           => 'اسم الفرع يجب ألا يتجاوز 255 حرفاً.',

            'address.required'   => 'العنوان مطلوب.',
            'address.string'     => 'العنوان يجب أن يكون نصاً.',
            'address.max'        => 'العنوان يجب ألا يتجاوز 255 حرفاً.',

            'code.required'      => 'كود الفرع مطلوب.',
            'code.string'        => 'كود الفرع يجب أن يكون نصاً.',
            'code.max'           => 'كود الفرع يجب ألا يتجاوز 50 حرفاً.',
            'code.unique'        => 'كود الفرع مستخدم من قبل.',

            'country_code.required' => 'كود الدولة مطلوب.',
            'country_code.string'   => 'كود الدولة يجب أن يكون نصاً.',
            'country_code.max'      => 'كود الدولة يجب ألا يتجاوز 10 رموز.',

            'phone.regex'         => 'رقم الهاتف يجب أن يحتوي فقط على أرقام بطول بين 7 و 15 خانة.',

            'email.email'        => 'البريد الإلكتروني غير صالح.',
            'email.max'          => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرفاً.',

            'manager_name.string' => 'اسم المدير يجب أن يكون نصاً.',
            'manager_name.max'   => 'اسم المدير يجب ألا يتجاوز 255 حرفاً.',

            'is_active.boolean'  => 'حقل التفعيل يجب أن يكون صحيح أو خطأ (true/false).',
        ];
    }
}
