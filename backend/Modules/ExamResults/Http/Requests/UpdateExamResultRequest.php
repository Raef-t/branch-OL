<?php

namespace Modules\ExamResults\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateExamResultRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'exam_id' => 'sometimes|required|integer|exists:exams,id',
            'student_id' => 'sometimes|required|integer|exists:students,id',
            'obtained_marks' => 'sometimes|required|numeric|min:0',
            'is_passed' => 'sometimes|nullable|boolean',
            'remarks' => 'sometimes|nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'exam_id.required' => 'معرف الامتحان مطلوب',
            'exam_id.exists' => 'الامتحان غير موجود',
            'student_id.required' => 'معرف الطالب مطلوب',
            'student_id.exists' => 'الطالب غير موجود',
            'obtained_marks.required' => 'العلامة المحصلة مطلوبة',
            'obtained_marks.numeric' => 'العلامة المحصلة يجب أن تكون رقم',
            'obtained_marks.min' => 'العلامة المحصلة يجب ألا تكون سالبة',
            'is_passed.boolean' => 'حالة النجاح يجب أن تكون صحيح أو خطأ',
            'remarks.string' => 'الملاحظات يجب أن تكون نص',
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