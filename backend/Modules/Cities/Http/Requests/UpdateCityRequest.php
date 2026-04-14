<?php

namespace Modules\Cities\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $cityId = $this->route('id'); // أو $this->city لو عندك Route Model Binding

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name')->ignore($cityId), // ✅ تجاهل السجل الحالي
            ],
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم المدينة مطلوب',
            'name.max'      => 'اسم المدينة يجب ألا يزيد عن 255 حرف',
            'name.unique'   => 'اسم المدينة مستخدم مسبقًا',

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
