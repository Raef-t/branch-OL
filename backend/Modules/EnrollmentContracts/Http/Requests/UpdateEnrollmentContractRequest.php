<?php

namespace Modules\EnrollmentContracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEnrollmentContractRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'sometimes|required|integer|exists:students,id',
            'total_amount_usd' => 'sometimes|required|numeric|min:0',
            'discount_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
            'discount_amount' => 'sometimes|nullable|numeric|min:0',
            'discount_reason' => 'nullable|string', 
            'final_amount_usd' => 'sometimes|required|numeric|min:0',
            'exchange_rate_at_enrollment' => 'sometimes|required|numeric|min:0',
            'final_amount_syp' => 'sometimes|required|numeric|min:0',
            'agreed_at' => 'sometimes|required|date',
            'installments_start_date' => 'sometimes|nullable|date',
            'description' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'معرف الطالب مطلوب',
            'student_id.exists' => 'الطالب غير موجود',
            'total_amount_usd.required' => 'المبلغ الكلي بالدولار مطلوب',
            'total_amount_usd.numeric' => 'المبلغ الكلي بالدولار يجب أن يكون رقم',
            'total_amount_usd.min' => 'المبلغ الكلي بالدولار يجب ألا يكون سالب',
            'discount_percentage.numeric' => 'نسبة الخصم يجب أن تكون رقم',
            'discount_percentage.min' => 'نسبة الخصم يجب ألا تكون سالبة',
            'discount_percentage.max' => 'نسبة الخصم يجب ألا تتجاوز 100',
            'discount_amount.numeric' => 'مبلغ الخصم يجب أن يكون رقم',
            'discount_amount.min' => 'مبلغ الخصم يجب ألا يكون سالب',
            'final_amount_usd.required' => 'المبلغ النهائي بالدولار مطلوب',
            'final_amount_usd.numeric' => 'المبلغ النهائي بالدولار يجب أن يكون رقم',
            'final_amount_usd.min' => 'المبلغ النهائي بالدولار يجب ألا يكون سالب',
            'exchange_rate_at_enrollment.required' => 'سعر الصرف عند التسجيل مطلوب',
            'exchange_rate_at_enrollment.numeric' => 'سعر الصرف عند التسجيل يجب أن يكون رقم',
            'exchange_rate_at_enrollment.min' => 'سعر الصرف عند التسجيل يجب ألا يكون سالب',
            'final_amount_syp.required' => 'المبلغ النهائي بالليرة السورية مطلوب',
            'final_amount_syp.numeric' => 'المبلغ النهائي بالليرة السورية يجب أن يكون رقم',
            'final_amount_syp.min' => 'المبلغ النهائي بالليرة السورية يجب ألا يكون سالب',
            'agreed_at.required' => 'تاريخ الاتفاق مطلوب',
            'agreed_at.date' => 'تاريخ الاتفاق يجب أن يكون تاريخ صالح',
            'description.string' => 'الوصف يجب أن يكون نص',
            'is_active.boolean' => 'حالة التفعيل يجب أن تكون صحيح أو خطأ',
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