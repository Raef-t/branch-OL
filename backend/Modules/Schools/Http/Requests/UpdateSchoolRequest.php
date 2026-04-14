<?php

namespace Modules\Schools\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'sometimes|required|string|max:255',
            'type'      => 'nullable|in:public,private,other',
            'city'      => 'nullable|string|max:255',
            'notes'     => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
