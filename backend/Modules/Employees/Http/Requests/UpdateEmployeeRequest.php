<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'sometimes|nullable|integer|exists:users,id|unique:employees,user_id,' . $this->route('id'),
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'job_title' => 'sometimes|nullable|string|max:255',
            'job_type' => 'sometimes|required|in:accountant,supervisor,coordinator',
            'hire_date' => 'sometimes|required|date',
            'phone' => 'sometimes|nullable|string|max:20',
            'institute_branch_id' => 'sometimes|required|integer|exists:institute_branches,id',
            'is_active' => 'sometimes|nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => 'المستخدم غير موجود',
            'user_id.unique' => 'هذا المستخدم مرتبط بموظف آخر',
            'first_name.required' => 'الاسم الأول مطلوب',
            'first_name.max' => 'الاسم الأول يجب ألا يزيد عن 255 حرف',
            'last_name.max' => 'الكنية يجب ألا تزيد عن 255 حرف',
            'job_title.max' => 'المسمى الوظيفي يجب ألا يزيد عن 255 حرف',
            'job_type.required' => 'نوع الوظيفة مطلوب',
            'job_type.in' => 'نوع الوظيفة يجب أن يكون accountant أو supervisor أو coordinator',
            'hire_date.required' => 'تاريخ التوظيف مطلوب',
            'hire_date.date' => 'تاريخ التوظيف يجب أن يكون تاريخ صالح',
            'phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 حرف',
            'institute_branch_id.required' => 'معرف فرع المعهد مطلوب',
            'institute_branch_id.exists' => 'فرع المعهد غير موجود',
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