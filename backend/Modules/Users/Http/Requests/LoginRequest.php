<?php

namespace Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unique_id'   => 'required|string|max:50',
            'password'    => 'required|string|min:6',

            // ✅ يمكن إرسال FCM token اختياريًا — لكن لو أرسل، يجب ألا يتجاوز الطول المعقول
            'fcm_token'   => 'nullable|string|max:1000',

            // ✅ device_info كائن JSON يحتوي على بيانات الجهاز (اختياري)
            'device_info' => 'nullable|array',

            // الحقول الداخلية ضمن device_info (اختيارية لكن موثقة)
            'device_info.model'        => 'nullable|string|max:255',
            'device_info.os'           => 'nullable|string|max:255',
            'device_info.app_version'  => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'unique_id.required'  => 'المعرّف الفريد مطلوب.',
            'unique_id.string'    => 'المعرّف الفريد يجب أن يكون نصًا.',
            'password.required'   => 'كلمة المرور مطلوبة.',
            'password.min'        => 'كلمة المرور يجب ألا تقل عن 6 أحرف.',
            'fcm_token.string'    => 'رمز FCM يجب أن يكون نصًا.',
            'fcm_token.max'       => 'رمز FCM طويل جدًا، الرجاء التحقق من صحته.',
            'device_info.array'   => 'يجب إرسال معلومات الجهاز بصيغة JSON صحيحة.',
            'device_info.model.string'       => 'حقل model يجب أن يكون نصًا.',
            'device_info.os.string'          => 'حقل os يجب أن يكون نصًا.',
            'device_info.app_version.string' => 'حقل app_version يجب أن يكون نصًا.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'فشل التحقق من البيانات المدخلة.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
