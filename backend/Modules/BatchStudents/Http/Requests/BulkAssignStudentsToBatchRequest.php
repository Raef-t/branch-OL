<?php

namespace Modules\BatchStudents\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkAssignStudentsToBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_id'     => 'required|integer|exists:batches,id',
            'student_ids'  => 'required|array|min:1',
            'student_ids.*' => 'required|integer|exists:students,id',
        ];
    }

    public function messages(): array
    {
        return [
            'batch_id.required'     => 'معرف الشعبة مطلوب',
            'batch_id.integer'      => 'معرف الشعبة يجب أن يكون عدد صحيح',
            'batch_id.exists'       => 'الشعبة غير موجودة',
            'student_ids.required'  => 'يجب تحديد طالب واحد على الأقل',
            'student_ids.array'     => 'معرفات الطلاب يجب أن تكون مصفوفة',
            'student_ids.min'       => 'يجب تحديد طالب واحد على الأقل',
            'student_ids.*.integer' => 'معرف الطالب يجب أن يكون عدد صحيح',
            'student_ids.*.exists'  => 'أحد الطلاب المحددين غير موجود في النظام',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
