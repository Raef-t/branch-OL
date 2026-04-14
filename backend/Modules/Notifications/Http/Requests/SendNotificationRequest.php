<?php

namespace Modules\Notifications\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendNotificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // تحويل المستلمين من JSON
        if ($this->has('recipients') && is_string($this->recipients)) {
            $this->merge([
                'recipients' => json_decode($this->recipients, true)
            ]);
        }

        // تحويل المرسل من JSON
        if ($this->has('sender') && is_string($this->sender)) {
            $this->merge([
                'sender' => json_decode($this->sender, true)
            ]);
        }

        // تحويل القيم إلى صغير
        if ($this->has('sender_type')) {
            $this->merge(['sender_type' => strtolower($this->sender_type)]);
        }

        if ($this->has('type')) {
            $this->merge(['type' => strtolower($this->type)]);
        }
    }

    public function rules()
    {
        return [
            // حقول المرسل
            'sender_type' => 'nullable|string|in:system,user,admin,employee,teacher',
            'sender_id' => 'nullable|integer',
            'sender_display_name' => 'nullable|string|max:255',

            // حقول الإشعار
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:sms,in_app,email,media,all',

            // المستلمين
            'recipients' => 'required|array|min:1',
            'recipients.*.id' => 'required|integer',
            'recipients.*.model_type' => 'required|string|in:student,parent,staff', // مُحدّث

            // المرفقات
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ];
    }

    public function messages()
    {
        return [
            // رسائل المرسل
            'sender_type.in' => 'نوع المرسل يجب أن يكون: system, user, admin, employee, teacher',
            'sender_id.integer' => 'معرف المرسل يجب أن يكون رقمًا صحيحًا',
            'sender_display_name.max' => 'اسم المرسل يجب ألا يزيد عن 255 حرفًا',

            // رسائل الإشعار
            'title.required' => 'حقل العنوان مطلوب',
            'title.string' => 'حقل العنوان يجب أن يكون نصًا',
            'title.max' => 'العنوان يجب ألا يزيد عن 255 حرفًا',
            'body.required' => 'محتوى الإشعار مطلوب',
            'body.string' => 'محتوى الإشعار يجب أن يكون نصًا',
            'type.required' => 'نوع الإشعار مطلوب',
            'type.in' => 'نوع الإشعار يجب أن يكون: sms, in_app, email, media, all',

            // رسائل المستلمين (مُحدّثة)
            'recipients.required' => 'يجب تحديد المستلمين',
            'recipients.array' => 'المستلمين يجب أن يكونوا مصفوفة',
            'recipients.min' => 'يجب تحديد مستلم واحد على الأقل',
            'recipients.*.id.required' => 'معرّف المستلم مطلوب',
            'recipients.*.id.integer' => 'معرّف المستلم يجب أن يكون رقمًا صحيحًا',
            'recipients.*.model_type.required' => 'نوع المستلم مطلوب',
            'recipients.*.model_type.in' => 'نوع المستلم يجب أن يكون: student, parent, staff', // مُحدّث

            // رسائل المرفقات
            'attachments.array' => 'المرفقات يجب أن تكون مصفوفة',
            'attachments.*.file' => 'كل مرفق يجب أن يكون ملفًا',
            'attachments.*.mimes' => 'نوع الملف غير مدعوم (مسموح: jpg, jpeg, png, pdf, doc, docx)',
            'attachments.*.max' => 'حجم الملف يجب ألا يتجاوز 5 ميغابايت',
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

    public function getSenderData(): array
    {
        $senderType = $this->input('sender_type', 'system');
        $senderId = $senderType === 'system' ? null : $this->input('sender_id');
        $senderDisplayName = $this->input('sender_display_name');

        // إذا كان النظام
        if ($senderType === 'system' && empty($senderDisplayName)) {
            $senderDisplayName = __('النظام');
        }

        return [
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'sender_display_name' => $senderDisplayName,
        ];
    }

    /**
     * الحصول على بيانات المستلمين
     */
    public function getRecipientsData(): array
    {
        $recipients = $this->input('recipients', []);

        return array_map(function ($recipient) {
            return [
                'recipient_id' => $recipient['id'],
                'recipient_type' => $recipient['model_type'],
                'status' => 'pending',
            ];
        }, $recipients);
    }
}
