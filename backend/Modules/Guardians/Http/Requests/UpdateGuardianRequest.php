<?php

namespace Modules\Guardians\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateGuardianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'family_id' => 'sometimes|nullable|integer|exists:families,id',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'national_id' => 'sometimes|nullable|string|max:50',
            'phone' => 'sometimes|nullable|string|max:20',
            'is_primary_contact' => 'sometimes|nullable|boolean',

            'occupation' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string',
            'relationship' => 'sometimes|nullable|in:father,mother,legal_guardian,other',
        ];
    }

    public function messages()
    {
        return [
            'family_id.exists' => 'العائلة غير موجودة',
            'first_name.required' => 'الاسم الأول مطلوب',
            'first_name.max' => 'الاسم الأول يجب ألا يزيد عن 255 حرف',
            'last_name.max' => 'الكنية يجب ألا تزيد عن 255 حرف',
            'national_id.max' => 'الرقم الوطني يجب ألا يزيد عن 50 حرف',
            'phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 حرف',
            'is_primary_contact.boolean' => 'حالة الاتصال الأساسي يجب أن تكون صحيح أو خطأ',


            'occupation.max' => 'المهنة يجب ألا تزيد عن 255 حرف',
            'relationship.in' => 'العلاقة يجب أن تكون father أو mother أو legal_guardian أو other',
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
