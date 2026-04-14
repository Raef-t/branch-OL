<?php

namespace Modules\ContactDetails\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreContactDetailRequest extends FormRequest
{
    public function authorize()
    {
        return true; // إذا عندك صلاحيات تحقق ضعها هنا
    }

    public function rules()
    {
        return [
            'guardian_id' => 'nullable|exists:guardians,id',
            'student_id' => 'nullable|exists:students,id',
            'family_id' => 'nullable|exists:families,id',
            'type' => 'required|in:phone,email,address,whatsapp,landline',
            'value' => 'nullable|string',
            'country_code' => 'nullable|required_if:type,phone|string|max:10',
            'phone_number' => 'nullable|required_if:type,phone,landline|string|max:30',
            'owner_type' => 'nullable|in:father,mother,student,sibling,relative,other,family',
            'owner_name' => 'nullable|string|max:100',
            'supports_call' => 'sometimes|nullable|boolean',
            'supports_whatsapp' => 'sometimes|nullable|boolean',
            'supports_sms' => 'sometimes|nullable|boolean',
            'is_primary' => 'sometimes|nullable|boolean',
            'is_sms_stopped' => 'sometimes|nullable|boolean',
            'stop_sms_from' => 'nullable|date',
            'stop_sms_to' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $merged = [];
        if ($this->has('supports_call')) $merged['supports_call'] = $this->boolean('supports_call');
        if ($this->has('supports_whatsapp')) $merged['supports_whatsapp'] = $this->boolean('supports_whatsapp');
        if ($this->has('supports_sms')) $merged['supports_sms'] = $this->boolean('supports_sms');
        if ($this->has('is_sms_stopped')) $merged['is_sms_stopped'] = $this->boolean('is_sms_stopped');
        if ($this->has('is_primary')) $merged['is_primary'] = $this->boolean('is_primary');

        if (!empty($merged)) {
            $this->merge($merged);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');

            if ($type !== 'landline') {
                $hasAtLeastOnePurpose =
                    $this->boolean('supports_call') ||
                    $this->boolean('supports_whatsapp') ||
                    $this->boolean('supports_sms');

                if (!$hasAtLeastOnePurpose) {
                    $validator->errors()->add(
                        'supports_call',
                        'يجب تحديد استخدام واحد على الأقل للرقم (اتصال أو واتساب أو رسائل).'
                    );
                }
            } else {
                // للـ landline يشترط إرسال family_id
                if (empty($this->input('family_id'))) {
                    $validator->errors()->add(
                        'family_id',
                        'يجب ربط الهاتف الأرضي بعائلة (family_id).'
                    );
                }
            }

            $ownerType = $this->input('owner_type');

            // التوجيه الإجباري بناءً على owner_type
            if (in_array($ownerType, ['father', 'mother'])) {
                if (empty($this->input('guardian_id'))) {
                    $validator->errors()->add('guardian_id', 'يجب إرسال معرف ولي الأمر (guardian_id) عندما يكون المالك أب أو أم.');
                }
            } elseif ($ownerType === 'student') {
                if (empty($this->input('student_id'))) {
                    $validator->errors()->add('student_id', 'يجب إرسال معرف الطالب (student_id) عندما يكون المالك هو الطالب.');
                }
            } elseif (in_array($ownerType, ['sibling', 'relative', 'other', 'family'])) {
                if (empty($this->input('family_id'))) {
                    $validator->errors()->add('family_id', 'يجب إرسال معرف العائلة (family_id) عندما يكون المالك أخ/أخت أو قريب أو عائلة.');
                }
            } else {
                // في حال عدم إرسال owner_type، يجب على الأقل توفر ID واحد للربط
                $hasAnyId = !empty($this->input('guardian_id')) || !empty($this->input('student_id')) || !empty($this->input('family_id'));
                if (!$hasAnyId) {
                    $validator->errors()->add('owner_type', 'يجب تحديد نوع المالك (owner_type) وإرسال الـ ID الخاص به (guardian_id أو student_id أو family_id)، ولا يُقبل الاسم لوحده.');
                }
            }

            // 🟢 التحقق من شروط الرقم الأساسي (is_primary)
            if ($this->boolean('is_primary')) {
                if ($type !== 'phone') {
                    $validator->errors()->add('is_primary', 'الرقم الأساسي (is_primary) مسموح فقط لنوع الهاتف المحمول (phone).');
                }
                if (!$this->boolean('supports_sms')) {
                    $validator->errors()->add('is_primary', 'الرقم الأساسي (is_primary) يعتمد على استقبال رسائل، لذا يجب تفعيل (supports_sms).');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'guardian_id.required' => 'معرف الولي مطلوب',
            'guardian_id.exists' => 'الولي غير موجود',
            'student_id.exists' => 'الطالب غير موجود',
            'family_id.exists' => 'العائلة غير موجودة',
            'type.required' => 'نوع الاتصال مطلوب',
            'type.in' => 'نوع الاتصال يجب أن يكون من القيم: phone,email,address,whatsapp,landline',
            'value.required' => 'القيمة مطلوبة',
            'value.max' => 'القيمة يجب ألا تتجاوز 50 حرفًا',
            'country_code.required_if' => 'الحقل country_code مطلوب عندما يكون الهاتف محمول (phone).',
            'phone_number.required_if' => 'الحقل phone_number مطلوب عندما يكون type = phone أو landline.',

            'country_code.max' => 'رمز الدولة يجب ألا يتجاوز 5 أحرف',

            'phone_number.max' => 'رقم الهاتف يجب ألا يتجاوز 15 رقمًا',
            'owner_type.in' => 'نوع صاحب الرقم غير صالح',
            'owner_name.max' => 'اسم صاحب الرقم يجب ألا يتجاوز 100 حرف',
            'supports_call.boolean' => 'حقل supports_call يجب أن يكون true أو false',
            'supports_whatsapp.boolean' => 'حقل supports_whatsapp يجب أن يكون true أو false',
            'supports_sms.boolean' => 'حقل supports_sms يجب أن يكون true أو false',
            'is_primary.boolean' => 'الحالة الأساسية يجب أن تكون صحيح أو خطأ',
            'notes.string' => 'الملاحظات يجب أن تكون نص',
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
