<?php

namespace Modules\Buses\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:buses,name', // ✅ منع التكرار
            'capacity' => 'nullable|integer',
            'driver_name' => 'nullable|string|max:255',
            'route_description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم الباص مطلوب',
            'name.unique'   => 'اسم الباص مستخدم مسبقًا',
            'name.max'      => 'اسم الباص يجب ألا يزيد عن 100 حرف',

            'capacity.integer' => 'السعة يجب أن تكون رقم صحيح',
            'driver_name.max'  => 'اسم السائق يجب ألا يزيد عن 255 حرف',
            'is_active.boolean' => 'حالة الباص يجب أن تكون صحيح أو خطأ',
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
