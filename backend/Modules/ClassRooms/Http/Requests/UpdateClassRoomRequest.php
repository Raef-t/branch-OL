<?php

namespace Modules\ClassRooms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClassRoomRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'name'     => 'sometimes|string|max:255',
            'code'     => "sometimes|string|max:50|unique:class_rooms,code,$id",
            'capacity' => 'sometimes|integer|min:1',
            'notes'    => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'name.max' => 'اسم القاعة يجب ألا يتجاوز 255 حرفًا',

            'code.unique' => 'رمز القاعة مستخدم مسبقًا',
            'code.max' => 'رمز القاعة يجب ألا يتجاوز 50 حرفًا',

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
