<?php

namespace Modules\PaymentInstallments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePaymentInstallmentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // إذا عندك صلاحيات تحقق ضعها هنا
    }

    public function rules()
    {
        return [
            'enrollment_contract_id' => 'required|integer|exists:enrollment_contracts,id',
            'installment_number' => 'required|integer|min:1|unique:payment_installments,installment_number,NULL,id,enrollment_contract_id,' . $this->enrollment_contract_id,
            'due_date' => 'required|date|unique:payment_installments,due_date,NULL,id,enrollment_contract_id,' . $this->enrollment_contract_id,  // فريد لتجنب تكرار التواريخ
            'planned_amount_usd' => 'required|numeric|min:0',
            'exchange_rate_at_due_date' => 'required|numeric|min:0',
            'planned_amount_syp' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,overdue,skipped',
        ];
    }

    public function messages()
    {
        return [
            'enrollment_contract_id.required' => 'معرف عقد التسجيل مطلوب',
            'enrollment_contract_id.exists' => 'عقد التسجيل غير موجود',
            'installment_number.required' => 'رقم القسط مطلوب',
            'installment_number.integer' => 'رقم القسط يجب أن يكون عدد صحيح',
            'installment_number.min' => 'رقم القسط يجب أن يكون على الأقل 1',
            'installment_number.unique' => 'رقم القسط مكرر ضمن هذا العقد',
            'due_date.required' => 'تاريخ الاستحقاق مطلوب',
            'due_date.date' => 'تاريخ الاستحقاق يجب أن يكون تاريخ صالح',
            'due_date.unique' => 'تاريخ الاستحقاق مكرر ضمن هذا العقد',
            'planned_amount_usd.required' => 'المبلغ المخطط بالدولار مطلوب',
            'planned_amount_usd.numeric' => 'المبلغ المخطط بالدولار يجب أن يكون رقمي',
            'planned_amount_usd.min' => 'المبلغ المخطط بالدولار يجب أن يكون على الأقل 0',
            'exchange_rate_at_due_date.required' => 'سعر الصرف في تاريخ الاستحقاق مطلوب',
            'exchange_rate_at_due_date.numeric' => 'سعر الصرف في تاريخ الاستحقاق يجب أن يكون رقمي',
            'exchange_rate_at_due_date.min' => 'سعر الصرف في تاريخ الاستحقاق يجب أن يكون على الأقل 0',
            'planned_amount_syp.required' => 'المبلغ المخطط بالليرة مطلوب',
            'planned_amount_syp.numeric' => 'المبلغ المخطط بالليرة يجب أن يكون رقمي',
            'planned_amount_syp.min' => 'المبلغ المخطط بالليرة يجب أن يكون على الأقل 0',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة يجب أن تكون أحد القيم: pending, paid, overdue, skipped',
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