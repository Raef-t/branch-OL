<?php

namespace Modules\PaymentInstallments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\PaymentInstallments\Models\PaymentInstallment;

class UpdatePaymentInstallmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $installmentId = $this->route('id');
        $installment = PaymentInstallment::find($installmentId);

        return [
            'enrollment_contract_id' => 'sometimes|required|integer|exists:enrollment_contracts,id',
            'installment_number' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'unique:payment_installments,installment_number,' . $installmentId . ',id,enrollment_contract_id,' . ($installment ? $installment->enrollment_contract_id : ''),
            ],
            'due_date' => 'sometimes|required|date',
            'planned_amount_usd' => 'sometimes|required|numeric|min:0',
            'exchange_rate_at_due_date' => 'sometimes|required|numeric|min:0',
            'planned_amount_syp' => 'sometimes|required|numeric|min:0',
            'status' => [
                'sometimes',
                'required',
                'in:pending,paid,overdue,skipped',
                function ($attribute, $value, $fail) use ($installment) {
                    // منع تغيير حالة قسط مدفوع
                    if ($installment && $installment->status === 'paid' && $value !== 'paid') {
                        $fail('لا يمكن تغيير حالة قسط مدفوع.');
                    }
                },
            ],
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