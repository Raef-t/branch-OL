<?php

namespace Modules\MessageTemplates\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMessageTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'name'       => 'sometimes|required|string|max:255|unique:message_templates,name,' . $id,
            'type'       => 'sometimes|required|in:sms,in_app,email',
            'category'   => 'sometimes|required|in:general,attendance,absence,behavior,exam,financial',
            'subject'    => 'nullable|string|max:255',
            'body'       => 'sometimes|required|string',
            'is_active'  => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'اسم القالب مطلوب',
            'name.max'          => 'اسم القالب يجب ألا يزيد عن 255 حرف',
            'name.unique'       => 'اسم القالب مستخدم بالفعل',

            'type.required'     => 'نوع القالب مطلوب',
            'type.in'           => 'نوع القالب يجب أن يكون sms أو in_app أو email',

            'category.required' => 'تصنيف الرسالة مطلوب',
            'category.in'       => 'تصنيف الرسالة غير صالح',

            'subject.max'       => 'العنوان يجب ألا يزيد عن 255 حرف',
            'body.required'     => 'نص القالب مطلوب',

            'is_active.boolean' => 'حالة التفعيل يجب أن تكون صحيح أو خطأ',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
