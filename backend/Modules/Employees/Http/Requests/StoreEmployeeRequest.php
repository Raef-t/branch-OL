<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // إذا عندك صلاحيات تحقق ضعها هنا
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable|integer|exists:users,id|unique:employees,user_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'job_type' => 'required|in:accountant,supervisor,coordinator',
            'hire_date' => 'required|date', 
            'phone' => 'nullable|string|max:20',
            'institute_branch_id' => 'nullable|integer|exists:institute_branches,id',
            'is_active' => 'nullable|boolean',
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
             'institute_branch_id' => 'sometimes|required|exists:institute_branches,id', 
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