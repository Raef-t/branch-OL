<?php

namespace Modules\Families\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ActivateFamilyUserRequest extends FormRequest
{
    public function authorize()
    {
        // يمكنك لاحقًا التحقق من الصلاحية هنا
        return true;
    }

    public function rules()
    {
        return [
            // لا توجد بيانات مطلوبة في الطلب (POST فارغ)
            // لكن نبقيه لتوحيد البنية وتمكين middleware/الصلاحيات
        ];
    }

    public function messages()
    {
        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'فشل التحقق من البيانات',
            'errors' => $validator->errors()
        ], 422));
    }
}