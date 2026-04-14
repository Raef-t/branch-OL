<?php

namespace Modules\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // إذا عندك صلاحيات تحقق ضعها هنا
    }

    public function rules()
    {
        return [
            'receipt_number' => 'required|string|max:255|unique:payments,receipt_number',
            'institute_branch_id' => 'required|integer|exists:institute_branches,id',
            'student_id' => 'required|integer|exists:students,id',
            'amount_usd' => 'nullable|numeric|min:0',
            'amount_syp' => 'nullable|numeric|min:0',
            // تعديل هنا: مطلوب فقط إذا كانت العملة SYP
            'exchange_rate_at_payment' => 'nullable|numeric|min:0|required_if:currency,SYP',
            'currency' => 'required|in:USD,SYP',
            'paid_date' => 'nullable|date',
            'description' => 'nullable|string',
            'reason' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'receipt_number.required' => 'رقم الإيصال مطلوب',
            'receipt_number.unique' => 'رقم الإيصال مستخدم بالفعل',
            'institute_branch_id.required' => 'معرف فرع المعهد مطلوب',
            'institute_branch_id.exists' => 'فرع المعهد غير موجود',
            'student_id.required' => 'معرف الطالب مطلوب',
            'student_id.exists' => 'الطالب غير موجود',
            'amount_usd.numeric' => 'المبلغ بالدولار يجب أن يكون رقمي',
            'amount_usd.min' => 'المبلغ بالدولار يجب أن يكون على الأقل 0',
            'amount_syp.numeric' => 'المبلغ بالليرة السورية يجب أن يكون رقمي',
            'amount_syp.min' => 'المبلغ بالليرة السورية يجب أن يكون على الأقل 0',
            'exchange_rate_at_payment.required_if' => 'سعر الصرف مطلوب إذا كانت العملة بالليرة السورية',
            'exchange_rate_at_payment.numeric' => 'سعر الصرف يجب أن يكون رقمي',
            'exchange_rate_at_payment.min' => 'سعر الصرف يجب أن يكون على الأقل 0',
            'currency.required' => 'العملة مطلوبة',
            'currency.in' => 'العملة يجب أن تكون USD أو SYP',
            'paid_date.date' => 'تاريخ الدفع يجب أن يكون تاريخ صالح',
            'description.string' => 'الوصف يجب أن يكون نصي', // <-- تم التعديل هنا
            'reason.string' => 'السبب يجب أن يكون نصي',
            'reason.max' => 'السبب لا يجب أن يتجاوز 1000 حرف',
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
