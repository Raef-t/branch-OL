<?php

namespace Modules\DoorSessions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UseDoorSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // الطالب مصادق فعلاً عبر sanctum middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'session_token' => ['required', 'string', 'exists:door_sessions,session_token'],
        ];
    }

    public function messages()
    {
        return [
            'session_token.required' => 'رمز الجلسة مطلوب.',
            'session_token.exists'   => 'رمز الجلسة غير صالح أو منتهي.',
        ];
    }
}
