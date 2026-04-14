<?php

namespace Modules\Instructors\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateInstructorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'user_id' => 'nullable|integer|exists:users,id|unique:instructors,user_id,' . $id,
            'name' => 'sometimes|required|string|max:255',
            'institute_branch_id' => 'sometimes|required|integer|exists:institute_branches,id',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'hire_date' => 'sometimes|required|date',
            'preferences' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => 'المستخدم غير موجود',
            'user_id.unique' => 'المستخدم مرتبط بالفعل بمدرب آخر',
            'name.required' => 'الاسم مطلوب',
            'name.max' => 'الاسم يجب ألا يزيد عن 255 حرف',
            'institute_branch_id.required' => 'معرف فرع المعهد مطلوب',
            'institute_branch_id.exists' => 'فرع المعهد غير موجود',
            'phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 حرف',
            'specialization.max' => 'التخصص يجب ألا يزيد عن 255 حرف',
            'hire_date.required' => 'تاريخ التوظيف مطلوب',
            'hire_date.date' => 'تاريخ التوظيف يجب أن يكون تاريخ صالح',
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