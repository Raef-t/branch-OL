<?php

namespace Modules\Cities\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'        => 'required|string|max:255|unique:cities,name', // ✅ منع التكرار
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم المدينة مطلوب',
            'name.unique'   => 'اسم المدينة مستخدم مسبقًا',
            'name.max'      => 'اسم المدينة يجب ألا يزيد عن 255 حرف',

            'description.string' => 'الوصف يجب أن يكون نصاً',
            'is_active.boolean'  => 'حالة المدينة يجب أن تكون صحيح أو خطأ',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'خطأ في التحقق من البيانات',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
