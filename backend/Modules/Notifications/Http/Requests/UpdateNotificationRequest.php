<?php

namespace Modules\Notifications\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // حقول المرسل (اختيارية للتحديث)
            'sender_type' => 'sometimes|nullable|string|in:system,user,admin,employee,teacher',
            'sender_id' => 'sometimes|nullable|integer',
            'sender_display_name' => 'sometimes|nullable|string|max:255',

            // حقول القالب
            'template_id' => 'sometimes|nullable|integer|exists:message_templates,id',

            // حقول الإشعار
            'title' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:sms,in_app,email,media,all',

            // حقول الهدف (مُوحّدة)
            'target_type' => 'sometimes|nullable|string|in:student,parent,staff,all',
            'target_id' => 'sometimes|nullable|integer',

            // تواريخ
            'scheduled_at' => 'sometimes|nullable|date|after_or_equal:now',
            'sent_at' => 'sometimes|nullable|date',

            // الحالة
            'status' => 'sometimes|required|in:pending,sent,failed,cancelled',
        ];
    }

    public function messages()
    {
        return [
            // نفس الرسائل من StoreNotificationRequest
            'sender_type.in' => 'نوع المرسل يجب أن يكون: system, user, admin, employee, teacher',
            'sender_id.integer' => 'معرف المرسل يجب أن يكون رقمًا صحيحًا',
            'sender_display_name.max' => 'اسم المرسل يجب ألا يزيد عن 255 حرفًا',
            'template_id.exists' => 'قالب الرسالة غير موجود',
            'title.required' => 'حقل العنوان مطلوب',
            'title.max' => 'العنوان يجب ألا يزيد عن 255 حرفًا',
            'body.required' => 'محتوى الإشعار مطلوب',
            'type.required' => 'نوع الإشعار مطلوب',
            'type.in' => 'نوع الإشعار يجب أن يكون: sms, in_app, email, media, all',
            'target_type.in' => 'نوع الجمهور يجب أن يكون: student, parent, staff, all', // مُحدّث
            'target_id.integer' => 'معرف الهدف يجب أن يكون رقمًا صحيحًا',
            'scheduled_at.date' => 'تاريخ الجدولة يجب أن يكون تاريخًا صالحًا',
            'scheduled_at.after_or_equal' => 'تاريخ الجدولة يجب أن يكون اليوم أو تاريخًا مستقبليًا',
            'sent_at.date' => 'تاريخ الإرسال يجب أن يكون تاريخًا صالحًا',
            'status.required' => 'حالة الإشعار مطلوبة',
            'status.in' => 'الحالة يجب أن تكون: pending, sent, failed, cancelled',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'فشل التحقق من صحة البيانات',
            'errors' => $validator->errors()
        ], 422));
    }

    protected function prepareForValidation()
    {
        // نفس منطق prepareForValidation من StoreNotificationRequest
        if ($this->has('sender_type')) {
            $this->merge(['sender_type' => strtolower($this->sender_type)]);
        }

        if ($this->has('type')) {
            $this->merge(['type' => strtolower($this->type)]);
        }

        if ($this->has('target_type')) {
            $this->merge(['target_type' => strtolower($this->target_type)]);
        }

        if ($this->has('status')) {
            $this->merge(['status' => strtolower($this->status)]);
        }
    }
}
