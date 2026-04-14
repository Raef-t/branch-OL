<?php
namespace Modules\Permissions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // يمكن إضافة تحقق من الصلاحية هنا
    }

    public function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'role_name' => 'required|string|exists:roles,name',
        ];
    }
}
