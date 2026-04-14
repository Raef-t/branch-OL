<?php

namespace Modules\DoorSessions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateSessionRequest extends FormRequest
{
    /**
     * Determine if the user/device is authorized to make this request.
     * We will handle device authorization via middleware, so allow here.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // The identifier of the door device requesting a new session QR
            'device_id' => ['required', 'string', 'exists:door_devices,device_id'],
        ];
    }

    /**
     * Customize the validation messages.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'device_id.required' => 'حقل device_id مطلوب.',
            'device_id.string'   => 'يجب أن يكون device_id نصًا.',
            'device_id.exists'   => 'الجهاز المطلوب غير مسجل.',
        ];
    }
}