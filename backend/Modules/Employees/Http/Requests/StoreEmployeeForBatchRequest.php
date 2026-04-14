<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEmployeeForBatchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        /**
         * هذا الريكوست الآن مخصص فقط لتعيين موظف على دفعة (أو تحديث تعيينه)
         * عبر جدول batch_employees.
         *
         * يُستخدم مع الراوت:
         * POST /api/employees/{id}/assign-to-batch
         */

        return [
            // الدفعة المراد تعيين الموظف عليها
            'batch_id'             => 'required|integer|exists:batches,id',

            // دور الموظف داخل الدفعة (مشرف، منسق، ... إلخ)
            'role'                 => 'sometimes|nullable|string|max:50',

            // تفاصيل التعيين
            'assignment_date'      => 'sometimes|date',
            'assigned_by'          => 'sometimes|nullable|integer|exists:users,id',
            'notes'                => 'sometimes|nullable|string',

            // حالة تفعيل التعيين نفسه
            'assignment_is_active' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'batch_id.required'           => 'معرّف الدفعة مطلوب.',
            'batch_id.integer'            => 'معرّف الدفعة يجب أن يكون رقمًا صحيحًا.',
            'batch_id.exists'             => 'الدفعة المحددة غير موجودة.',

            'role.max'                    => 'الدور يجب ألا يزيد عن 50 حرفًا.',

            'assignment_date.date'        => 'تاريخ التعيين يجب أن يكون تاريخًا صالحًا.',
            'assigned_by.integer'         => 'المستخدم الذي قام بالتعيين يجب أن يكون رقمًا صحيحًا.',
            'assigned_by.exists'          => 'المستخدم الذي قام بالتعيين غير موجود.',
            'assignment_is_active.boolean'=> 'حالة تفعيل التعيين يجب أن تكون صحيح أو خطأ.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
