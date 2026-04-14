<?php

namespace Modules\DoorDevices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDoorDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'device_id' => 'sometimes|required|string|unique:door_devices,device_id,' . $this->route('id'),
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'is_active' => 'sometimes|nullable|boolean',
            'last_seen_at' => 'sometimes|nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'device_id.required' => 'معرف الجهاز مطلوب',
            'device_id.unique' => 'معرف الجهاز موجود مسبقاً',
            'device_id.max' => 'معرف الجهاز يجب ألا يزيد عن 100 حرف',
            'name.required' => 'اسم الجهاز مطلوب',
            'name.max' => 'اسم الجهاز يجب ألا يزيد عن 255 حرف',
            'location.max' => 'الموقع يجب ألا يزيد عن 255 حرف',
            'is_active.boolean' => 'حالة التفعيل يجب أن تكون صحيح أو خطأ',
            'last_seen_at.date' => 'آخر مرة رؤية يجب أن تكون تاريخ صالح',
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