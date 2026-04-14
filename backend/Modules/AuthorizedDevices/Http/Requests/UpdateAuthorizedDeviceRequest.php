<?php

namespace Modules\AuthorizedDevices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAuthorizedDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'device_id' => 'sometimes|required|string|unique:authorized_devices,device_id,' . $this->route('id'),
            'device_name' => 'sometimes|nullable|string|max:255',
            'is_active' => 'sometimes|nullable|boolean',
            'last_used_at' => 'sometimes|nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function messages()
    {
        return [
            'device_id.required' => 'معرف الجهاز مطلوب',
            'device_id.unique' => 'معرف الجهاز موجود مسبقاً',
            'device_id.max' => 'معرف الجهاز يجب ألا يتجاوز 100 حرف',
            'device_name.max' => 'اسم الجهاز يجب ألا يتجاوز 255 حرف',
            'is_active.boolean' => 'حالة التفعيل يجب أن تكون صحيح أو خطأ',
            'last_used_at.date_format' => 'صيغة آخر استخدام يجب أن تكون Y-m-d H:i:s',
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