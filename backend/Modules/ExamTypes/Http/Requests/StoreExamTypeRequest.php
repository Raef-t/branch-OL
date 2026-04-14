<?php

namespace Modules\ExamTypes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreExamTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // إذا عندك صلاحيات تحقق ضعها هنا
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:exam_types,name',
            'description' => 'nullable|string',
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