<?php

namespace Modules\ContactDetails\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Modules\ContactDetails\Models\ContactDetail;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateContactDetailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // استخرج النوع الحالي من الإدخال أو من السجل
        // ملاحظة: اسم باراميتر الـroute قد يكون contactDetail أو contact_detail
        $routeParam = $this->route('contactDetail') ?? $this->route('contact_detail');
        $routeModel = $routeParam instanceof ContactDetail ? $routeParam : ContactDetail::find($routeParam);
        $type = $this->input('type', $routeModel?->type ?? null);

        return [
            'guardian_id' => 'sometimes|nullable|integer|exists:guardians,id',
            'student_id' => 'sometimes|nullable|integer|exists:students,id',
            'family_id' => 'sometimes|nullable|integer|exists:families,id',
            'type' => ['sometimes', 'required', Rule::in(['phone', 'email', 'address', 'whatsapp', 'landline'])],

            'value' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $type && !in_array($type, ['phone', 'landline']))
            ],

            'country_code' => [
                'sometimes',
                'nullable',
                'string',
                'max:10',
                Rule::requiredIf(fn() => $type === 'phone')
            ],
            'phone_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:30',
                Rule::requiredIf(fn() => in_array($type, ['phone', 'landline']))
            ],

            'owner_type' => ['sometimes', 'nullable', Rule::in(['father', 'mother', 'student', 'sibling', 'relative', 'other', 'family'])],
            'owner_name' => 'sometimes|nullable|string|max:100',
            'supports_call' => 'sometimes|nullable|boolean',
            'supports_whatsapp' => 'sometimes|nullable|boolean',
            'supports_sms' => 'sometimes|nullable|boolean',

            'is_primary' => 'sometimes|nullable|boolean',
            'is_sms_stopped' => 'sometimes|nullable|boolean',
            'stop_sms_from' => 'sometimes|nullable|date',
            'stop_sms_to' => 'sometimes|nullable|date',
            'notes' => 'sometimes|nullable|string|max:500',
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
            $routeParam = $this->route('contactDetail') ?? $this->route('contact_detail');
            $routeModel = $routeParam instanceof ContactDetail ? $routeParam : ContactDetail::find($routeParam);

            $supportsCall = $this->has('supports_call')
                ? $this->boolean('supports_call')
                : (bool)($routeModel?->supports_call);

            $supportsWhatsapp = $this->has('supports_whatsapp')
                ? $this->boolean('supports_whatsapp')
                : (bool)($routeModel?->supports_whatsapp);

            $supportsSms = $this->has('supports_sms')
                ? $this->boolean('supports_sms')
                : (bool)($routeModel?->supports_sms);

            $type = $this->input('type', $routeModel?->type);

            if ($type !== 'landline') {
                if (!$supportsCall && !$supportsWhatsapp && !$supportsSms) {
                    $validator->errors()->add(
                        'supports_call',
                        'يجب تحديد استخدام واحد على الأقل للرقم (اتصال أو واتساب أو رسائل).'
                    );
                }
            } else {
                // للـ landline يشترط وجود family_id (سواء مُرسل في الطلب أو موجود مسبقاً)
                $familyId = $this->input('family_id', $routeModel?->family_id);
                if (empty($familyId)) {
                    $validator->errors()->add(
                        'family_id',
                        'يجب ربط الهاتف الأرضي بعائلة (family_id).'
                    );
                }
            }

            $ownerType = $this->input('owner_type', $routeModel?->owner_type);

            // التوجيه الإجباري بناءً على owner_type (في حالة التحديث يتم اعتبار القيمة القادمة أو المخزنة)
            if (in_array($ownerType, ['father', 'mother'])) {
                if (empty($this->input('guardian_id', $routeModel?->guardian_id))) {
                    $validator->errors()->add('guardian_id', 'يجب إرسال معرف ولي الأمر (guardian_id) عندما يكون المالك أب أو أم.');
                }
            } elseif ($ownerType === 'student') {
                if (empty($this->input('student_id', $routeModel?->student_id))) {
                    $validator->errors()->add('student_id', 'يجب إرسال معرف الطالب (student_id) عندما يكون المالك هو الطالب.');
                }
            } elseif (in_array($ownerType, ['sibling', 'relative', 'other', 'family'])) {
                if (empty($this->input('family_id', $routeModel?->family_id))) {
                    $validator->errors()->add('family_id', 'يجب إرسال معرف العائلة (family_id) عندما يكون المالك أخ أو قريب أو عائلة.');
                }
            } else {
                $hasAnyId = !empty($this->input('guardian_id', $routeModel?->guardian_id)) ||
                            !empty($this->input('student_id', $routeModel?->student_id)) ||
                            !empty($this->input('family_id', $routeModel?->family_id));

                if (!$hasAnyId) {
                    $validator->errors()->add('owner_type', 'يجب تحديد نوع المالك (owner_type) والـ ID الخاص به وإلغاء الاعتماد على الاسم فقط.');
                }
            }

            // 🟢 التحقق من شروط الرقم الأساسي (تخفيف القيود للسماح بالتبديل)
            $isPrimary = $this->has('is_primary') ? $this->boolean('is_primary') : (bool)($routeModel?->is_primary);
            if ($isPrimary && $type !== 'phone') {
                $validator->errors()->add('is_primary', 'الرقم الأساسي مسموح فقط للهواتف المحمولة.');
            }
        });
    }

    public function messages()
    {
        return [
            'guardian_id.exists' => 'الولي غير موجود',
            'student_id.exists' => 'الطالب غير موجود',
            'family_id.exists' => 'العائلة غير موجودة',
            'type.in' => 'نوع الاتصال يجب أن يكون من القيم: phone, email, address, whatsapp, landline',
            'value.required' => 'الحقل value مطلوب عندما يكون type ليس هاتفاً أو أرضي.',
            'value.string' => 'القيمة يجب أن تكون نصًا',
            'value.max' => 'القيمة يجب ألا تتجاوز 255 حرفًا',
            'country_code.required' => 'الحقل country_code مطلوب للهاتف المحمول.',
            'country_code.max' => 'رمز الدولة يجب ألا يتجاوز 5 أحرف',
            'phone_number.required' => 'الحقل phone_number مطلوب للهاتف المحمول والأرضي.',
            'phone_number.max' => 'رقم الهاتف يجب ألا يتجاوز 15 رقمًا',
            'owner_type.in' => 'نوع صاحب الرقم غير صالح',
            'owner_name.max' => 'اسم صاحب الرقم يجب ألا يتجاوز 100 حرف',
            'supports_call.boolean' => 'حقل supports_call يجب أن يكون true أو false',
            'supports_whatsapp.boolean' => 'حقل supports_whatsapp يجب أن يكون true أو false',
            'supports_sms.boolean' => 'حقل supports_sms يجب أن يكون true أو false',
            'is_primary.boolean' => 'الحالة الأساسية يجب أن تكون true أو false',
            'notes.string' => 'الملاحظات يجب أن تكون نصًا',
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
