<?php

namespace Modules\Families\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FamiliesUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $familyId = $this->route('id');

        return [
            'user_id' => 'nullable|integer|exists:users,id|unique:families,user_id,' . $familyId,
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.integer'  => 'معرف المستخدم يجب أن يكون رقماً صحيحاً.',
            'user_id.exists'   => 'المستخدم المحدد غير موجود.',
            'user_id.unique'   => 'هذا المستخدم مرتبط بعائلة أخرى بالفعل.',
        ];
    }
}