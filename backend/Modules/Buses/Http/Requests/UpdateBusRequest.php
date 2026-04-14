<?php

namespace Modules\Buses\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $busId = $this->route('id'); // أو $this->bus لو تستخدم Route Model Binding

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('buses', 'name')->ignore($busId), // ✅ تجاهل السجل الحالي
            ],
            'capacity' => 'nullable|integer',
            'driver_name' => 'nullable|string|max:255',
            'route_description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
