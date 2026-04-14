<?php

namespace Modules\Permissions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveRoleRequest extends FormRequest
{
    public function authorize()
    {
        // ضبط الصلاحيات حسب الحاجة
        return true;
    }

    public function rules()
    {
        return [
            'user_id'   => ['required','integer','exists:users,id'],
            'role_name' => ['required','string','exists:roles,name'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required'   => 'معرّف المستخدم مطلوب.',
            'user_id.integer'    => 'معرّف المستخدم يجب أن يكون عددًا صحيحًا.',
            'user_id.exists'     => 'المستخدم المحدد غير موجود.',

            'role_name.required' => 'اسم الدور مطلوب.',
            'role_name.string'   => 'اسم الدور يجب أن يكون نصًا.',
            'role_name.exists'   => 'الدور المحدد غير موجود.',
        ];
    }
}
