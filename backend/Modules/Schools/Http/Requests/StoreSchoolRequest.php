<?php

namespace Modules\Schools\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'type'      => 'nullable|in:public,private,other',
            'city'      => 'nullable|string|max:255',
            'notes'     => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المدرسة مطلوب',
            'type.in'       => 'نوع المدرسة يجب أن يكون public أو private أو other',
        ];
    }
}
