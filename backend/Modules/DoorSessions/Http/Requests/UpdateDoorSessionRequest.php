<?php

namespace Modules\DoorSessions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDoorSessionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'device_id' => 'sometimes|required|integer|exists:door_devices,id',
            'session_token' => 'sometimes|required|string|unique:door_sessions,session_token,' . $this->route('id'),
            'expires_at' => 'sometimes|required|date',
            'is_used' => 'sometimes|nullable|boolean',
            'student_id' => 'sometimes|nullable|integer|exists:students,id',
            'used_at' => 'sometimes|nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'device_id.required' => 'معرف الجهاز مطلوب',
            'device_id.exists' => 'الجهاز غير موجود',
            'session_token.required' => 'رمز الجلسة مطلوب',
            'session_token.unique' => 'رمز الجلسة موجود مسبقاً',
            'session_token.max' => 'رمز الجلسة يجب ألا يزيد عن 255 حرف',
            'expires_at.required' => 'وقت الانتهاء مطلوب',
            'expires_at.date' => 'وقت الانتهاء يجب أن يكون تاريخ صالح',
            'is_used.boolean' => 'حالة الاستخدام يجب أن تكون صحيح أو خطأ',
            'student_id.exists' => 'الطالب غير موجود',
            'used_at.date' => 'وقت الاستخدام يجب أن يكون تاريخ صالح',
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