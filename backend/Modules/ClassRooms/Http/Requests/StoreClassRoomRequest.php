<?php

namespace Modules\ClassRooms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClassRoomRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:50|unique:class_rooms,code',
            'capacity' => 'required|integer|min:1',
            'notes'    => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم القاعة مطلوب',
            'name.max' => 'اسم القاعة يجب ألا يتجاوز 255 حرفًا',

            'code.required' => 'رمز القاعة مطلوب',
            'code.unique' => 'رمز القاعة مستخدم مسبقًا',
            'code.max' => 'رمز القاعة يجب ألا يتجاوز 50 حرفًا',

            'capacity.required' => 'السعة مطلوبة',
            'capacity.integer' => 'السعة يجب أن تكون رقمًا صحيحًا',
            'capacity.min' => 'السعة يجب ألا تقل عن 1',

            'notes.string' => 'الملاحظات يجب أن تكون نصًا',
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
