<?php

namespace Modules\FcmTokens\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FcmTokenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $method = $this->method();

        if ($method === 'POST') {
            return [
                'token' => 'required|string',
                'user_id' => 'nullable|exists:users,id',
                'device_info' => 'nullable|array',
            ];
        }

        if (in_array($method, ['PUT', 'PATCH'])) {
            return [
                'token' => 'sometimes|required|string',
                'user_id' => 'sometimes|nullable|exists:users,id',
                'device_info' => 'nullable|array',
                'last_seen' => 'nullable|date',
            ];
        }

        return [];
    }

    public function messages()
    {
        return [
            'token.required' => 'حقل التوكن مطلوب.',
            'token.string' => 'التوكن يجب أن يكون نصياً.',
            'user_id.exists' => 'المستخدم المحدد غير موجود.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}