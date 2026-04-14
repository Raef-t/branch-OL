<?php

namespace Modules\ExamTypes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateExamTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255|unique:exam_types,name,' . $this->id,
            'description' => 'sometimes|nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم نوع الامتحان مطلوب',
            'name.string' => 'اسم نوع الامتحان يجب أن يكون نص',
            'name.max' => 'اسم نوع الامتحان يجب ألا يزيد عن 255 حرف',
            'name.unique' => 'اسم نوع الامتحان موجود بالفعل',
            'description.string' => 'الوصف يجب أن يكون نص',
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