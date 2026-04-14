<?php

namespace Modules\Permissions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkAssignRolesRequest extends FormRequest
{
    public function authorize()
    {
        // يمكنك ضبط الصلاحيات حسب الحاجة
        return true;
    }

    public function rules()
    {
        return [
            'user_id'       => ['required', 'integer', 'exists:users,id'],
            'role_names'    => ['required', 'array', 'min:1'],
            'role_names.*'  => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required'      => 'معرّف المستخدم مطلوب.',
            'user_id.integer'       => 'معرّف المستخدم يجب أن يكون عددًا صحيحًا.',
            'user_id.exists'        => 'المستخدم المحدد غير موجود.',

            'role_names.required'   => 'قائمة الأدوار مطلوبة.',
            'role_names.array'      => 'قائمة الأدوار يجب أن تكون مصفوفة.',
            'role_names.min'        => 'يجب اختيار دوريْن على الأقل.',

            'role_names.*.required' => 'اسم الدور مطلوب لكل عنصر في القائمة.',
            'role_names.*.string'   => 'اسم الدور يجب أن يكون نصًا.',
            'role_names.*.exists'   => 'أحد الأدوار المحددة غير موجود.',
        ];
    }
}
