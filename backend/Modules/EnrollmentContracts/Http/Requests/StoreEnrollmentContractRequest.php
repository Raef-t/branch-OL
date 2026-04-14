<?php

namespace Modules\EnrollmentContracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEnrollmentContractRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'required|integer|exists:students,id',

            // USD
            'total_amount_usd' => 'nullable|numeric|min:0',
            'final_amount_usd' => 'nullable|numeric|min:0',

            // SYP
            'final_amount_syp' => 'nullable|numeric|min:0',
            'exchange_rate_at_enrollment' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $final_syp = $this->input('final_amount_syp');
                    if (!is_null($final_syp) && $final_syp > 0 && (is_null($value) || $value === '')) {
                        $fail('سعر الصرف مطلوب عند إدخال المبلغ بالليرة.');
                    }
                }
            ],

            // General
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string', 
            'agreed_at' => 'required|date',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',

            'mode' => 'required|in:automatic,manual',

            // Automatic mode
            'installments_start_date' => 'required_if:mode,automatic|date|after_or_equal:agreed_at',

            // Manual mode
            'installments_count' => 'required_if:mode,manual|integer|min:1',
            'installments' => 'required_if:mode,manual|array|min:1',
            'installments.*.installment_number' => 'required_if:mode,manual|integer|min:1|distinct',
            'installments.*.due_date' => 'required_if:mode,manual|date',
            'installments.*.planned_amount_usd' => 'required_if:mode,manual|numeric|min:0',
            'first_payment' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'first_payment.array' => 'بيانات الدفعة الأولى يجب أن تكون على شكل مصفوفة.',
            // student
            'student_id.required' => 'يجب اختيار الطالب.',
            'student_id.integer' => 'معرّف الطالب غير صالح.',
            'student_id.exists' => 'الطالب المحدد غير موجود.',

            // USD
            'total_amount_usd.numeric' => 'المبلغ الإجمالي بالدولار يجب أن يكون رقم.',
            'total_amount_usd.min' => 'المبلغ الإجمالي بالدولار لا يمكن أن يكون سالباً.',

            'final_amount_usd.numeric' => 'المبلغ النهائي بالدولار يجب أن يكون رقم.',
            'final_amount_usd.min' => 'المبلغ النهائي بالدولار لا يمكن أن يكون سالباً.',

            // SYP
            'final_amount_syp.numeric' => 'المبلغ النهائي بالليرة يجب أن يكون رقم.',
            'final_amount_syp.min' => 'المبلغ النهائي بالليرة لا يمكن أن يكون سالباً.',

            'exchange_rate_at_enrollment.required_with' =>
                'سعر الصرف مطلوب عند إدخال المبلغ بالليرة.',
            'exchange_rate_at_enrollment.numeric' =>
                'سعر الصرف يجب أن يكون رقم.',
            'exchange_rate_at_enrollment.min' =>
                'سعر الصرف يجب أن يكون أكبر أو يساوي صفر.',

            // Discount
            'discount_percentage.numeric' => 'نسبة الحسم يجب أن تكون رقم.',
            'discount_percentage.min' => 'نسبة الحسم لا يمكن أن تكون أقل من 0%.',
            'discount_percentage.max' => 'نسبة الحسم لا يمكن أن تتجاوز 100%.',
            'discount_amount.numeric' => 'قيمة الحسم يجب أن تكون رقم.',
            'discount_amount.min' => 'قيمة الحسم لا يمكن أن تكون أقل من 0.',
            'discount_reason.string' => 'سبب الخصم يجب أن يكون نص.', // ✅ رسالة خاصة بالحقل الجديد

            // Dates
            'agreed_at.required' => 'تاريخ الاتفاق مطلوب.',
            'agreed_at.date' => 'تاريخ الاتفاق غير صالح.',

            'installments_start_date.required_if' =>
                'تاريخ بدء الأقساط مطلوب عند اختيار النمط التلقائي.',
            'installments_start_date.date' =>
                'تاريخ بدء الأقساط غير صالح.',
            'installments_start_date.after_or_equal' =>
                'تاريخ بدء الأقساط يجب أن يكون بعد أو يساوي تاريخ الاتفاق.',

            // Mode
            'mode.required' => 'يجب تحديد نمط إنشاء الأقساط.',
            'mode.in' => 'نمط إنشاء الأقساط غير صالح.',

            // Manual installments
            'installments_count.required_if' =>
                'عدد الأقساط مطلوب عند اختيار النمط اليدوي.',
            'installments_count.integer' =>
                'عدد الأقساط يجب أن يكون رقم صحيح.',
            'installments_count.min' =>
                'عدد الأقساط يجب أن يكون على الأقل 1.',

            'installments.required_if' =>
                'يجب إدخال تفاصيل الأقساط عند اختيار النمط اليدوي.',
            'installments.array' =>
                'تفاصيل الأقساط يجب أن تكون على شكل مصفوفة.',
            'installments.min' =>
                'يجب إدخال قسط واحد على الأقل.',

            'installments.*.installment_number.required_if' =>
                'رقم القسط مطلوب.',
            'installments.*.installment_number.integer' =>
                'رقم القسط يجب أن يكون رقم صحيح.',
            'installments.*.installment_number.min' =>
                'رقم القسط يجب أن يكون 1 أو أكثر.',
            'installments.*.installment_number.distinct' =>
                'رقم القسط مكرر.',

            'installments.*.due_date.required_if' =>
                'تاريخ استحقاق القسط مطلوب.',
            'installments.*.due_date.date' =>
                'تاريخ استحقاق القسط غير صالح.',

            'installments.*.planned_amount_usd.required_if' =>
                'قيمة القسط بالدولار مطلوبة.',
            'installments.*.planned_amount_usd.numeric' =>
                'قيمة القسط يجب أن تكون رقم.',
            'installments.*.planned_amount_usd.min' =>
                'قيمة القسط لا يمكن أن تكون سالبة.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->mode === 'manual') {
                $installmentsCount = $this->input('installments_count');
                $installments = $this->input('installments', []);

                if (count($installments) !== (int)$installmentsCount) {
                    $validator->errors()->add(
                        'installments_count',
                        'عدد الأقساط يجب أن يساوي عدد عناصر الأقساط المدخلة.'
                    );
                }
            }
        });
    }

}
