<?php

namespace Modules\Notifications\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminNotificationIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],

            'from' => ['nullable', 'date', 'before_or_equal:to'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],

            'read' => ['nullable', 'boolean'],

            'sender_type' => ['nullable', 'in:admin,system,user,teacher,employee'],

            'has_attachments' => ['nullable', 'boolean'],

            'template_id' => ['nullable', 'integer', 'exists:notification_templates,id'],

            'status' => ['nullable', 'in:delivered,pending,failed'],

            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.integer' => 'معرّف المستخدم يجب أن يكون رقمًا صحيحًا',
            'user_id.exists'  => 'المستخدم المحدد غير موجود',

            'from.date' => 'تاريخ البداية غير صالح',
            'from.before_or_equal' => 'تاريخ البداية يجب أن يكون قبل أو يساوي تاريخ النهاية',

            'to.date' => 'تاريخ النهاية غير صالح',
            'to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',

            'read.boolean' => 'قيمة حالة القراءة يجب أن تكون true أو false',

            'sender_type.in' => 'نوع المرسل غير مدعوم',

            'has_attachments.boolean' => 'قيمة وجود المرفقات يجب أن تكون true أو false',

            'template_id.integer' => 'معرّف القالب يجب أن يكون رقمًا',
            'template_id.exists'  => 'القالب المحدد غير موجود',

            'status.in' => 'حالة التسليم يجب أن تكون: delivered أو pending أو failed',

            'per_page.integer' => 'عدد العناصر في الصفحة يجب أن يكون رقمًا',
            'per_page.min' => 'الحد الأدنى للعناصر في الصفحة هو عنصر واحد',
            'per_page.max' => 'الحد الأقصى للعناصر في الصفحة هو 100',

            'page.integer' => 'رقم الصفحة يجب أن يكون رقمًا صحيحًا',
            'page.min' => 'رقم الصفحة يجب أن يكون 1 أو أكثر',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'has_attachments' => $this->boolean('has_attachments'),
            'read' => $this->boolean('read'),
        ]);
    }


    protected function failedAuthorization()
    {
        abort(response()->json([
            'status' => false,
            'message' => 'غير مصرح لك بعرض الإشعارات الإدارية',
        ], 403));
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        abort(response()->json([
            'status' => false,
            'message' => 'بيانات الفلترة غير صحيحة',
            'errors' => $validator->errors(),
        ], 422));
    }
}
