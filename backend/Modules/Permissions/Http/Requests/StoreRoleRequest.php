<?php

namespace Modules\Permissions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // أو تحقق من الصلاحية: $this->user()->can('assign_roles')
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }
}