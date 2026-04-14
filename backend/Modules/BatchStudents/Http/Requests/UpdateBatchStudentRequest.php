<?php
namespace Modules\BatchStudents\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdateBatchStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'batch_id' => 'sometimes|required|integer|exists:batches,id',
            'student_id' => 'sometimes|required|integer|exists:students,id',
            'is_partial' => 'sometimes|boolean'
        ];
    }
    public function messages()
    {
        return [
            'batch_id.required' => 'معرف الدفعة مطلوب',
            'batch_id.integer' => 'معرف الدفعة يجب أن يكون عدد صحيح',
            'batch_id.exists' => 'الدفعة غير موجودة',
            'student_id.required' => 'معرف الطالب مطلوب',
            'student_id.integer' => 'معرف الطالب يجب أن يكون عدد صحيح',
            'student_id.exists' => 'الطالب غير موجود'
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