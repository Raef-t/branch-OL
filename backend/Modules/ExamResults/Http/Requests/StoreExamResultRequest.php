<?php

namespace Modules\ExamResults\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreExamResultRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'exam_id'        => 'required|integer|exists:exams,id',
            'student_id'     => [
                'required',
                'integer',
                'exists:students,id',
                Rule::unique('exam_results', 'student_id')
                    ->where(function ($query) {
                        return $query->where('exam_id', $this->input('exam_id'));
                    })
                    ->ignore($this->route('exam_result') ? $this->route('exam_result')->id : null),
            ],
            'obtained_marks' => 'required|numeric|min:0',
            'is_passed'      => 'nullable|boolean',
            'remarks'        => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'exam_id.required'          => 'معرف الامتحان مطلوب',
            'exam_id.integer'           => 'معرف الامتحان يجب أن يكون رقم صحيح',
            'exam_id.exists'            => 'الامتحان المحدد غير موجود',

            'student_id.required'       => 'معرف الطالب مطلوب',
            'student_id.integer'        => 'معرف الطالب يجب أن يكون رقم صحيح',
            'student_id.exists'         => 'الطالب المحدد غير موجود',
            'student_id.unique'         => 'هذا الطالب لديه نتيجة مسجلة مسبقًا في هذا الامتحان',

            'obtained_marks.required'   => 'العلامة المحصلة مطلوبة',
            'obtained_marks.numeric'    => 'العلامة المحصلة يجب أن تكون رقمًا',
            'obtained_marks.min'        => 'العلامة المحصلة لا يمكن أن تكون سالبة',

            'is_passed.boolean'         => 'حالة النجاح يجب أن تكون true أو false',
            'remarks.string'            => 'الملاحظات يجب أن تكون نصًا',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'خطأ في التحقق من البيانات',
            'errors'  => $validator->errors(),
        ], 422));
    }
}