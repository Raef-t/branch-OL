<?php

namespace Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // إذا عندك صلاحيات تحقق ضعها هنا
    }

    public function rules()
    {
        return [
            'unique_id' => 'required|string|max:255|unique:users,unique_id',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,staff,student,family',
            'is_approved' => 'nullable|boolean',
            'force_password_change' => 'nullable|boolean',
            'fcm_token' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'unique_id.required' => 'المعرف الفريد مطلوب',
            'unique_id.unique' => 'المعرف الفريد مستخدم بالفعل',
            'name.required' => 'الاسم مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف',
            'role.required' => 'الدور مطلوب',
            'role.in' => 'الدور يجب أن يكون أحد القيم: admin, staff, student, family',
            'is_approved.boolean' => 'حالة الموافقة يجب أن تكون صحيح أو خطأ',
            'force_password_change.boolean' => 'إجبار تغيير كلمة المرور يجب أن يكون صحيح أو خطأ',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422));
    }
}