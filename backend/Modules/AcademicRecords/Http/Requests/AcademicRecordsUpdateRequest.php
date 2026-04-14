<?php

namespace Modules\AcademicRecords\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademicRecordsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id'   => 'sometimes|required|integer|exists:students,id',
            'record_type'  => 'sometimes|nullable|string',
            'total_score'  => 'nullable|numeric|min:0',
            'year'         => 'nullable|integer|min:1900|max:' . date('Y'),
            'description'  => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'معرف الطالب مطلوب.',
            'student_id.integer'  => 'معرف الطالب يجب أن يكون رقماً صحيحاً.',
            'student_id.exists'   => 'الطالب المحدد غير موجود.',

            'record_type.required' => 'نوع السجل مطلوب.',
            'record_type.in'       => 'نوع السجل غير صالح. القيم المسموحة: ninth_grade, bac_failed, bac_passed, other.',

            'total_score.numeric' => 'المجموع يجب أن يكون رقماً.',
            'total_score.min'     => 'المجموع لا يمكن أن يكون أقل من 0.',
            'total_score.max'     => 'المجموع لا يمكن أن يتجاوز 100.',

            'year.integer' => 'السنة يجب أن تكون رقماً صحيحاً.',
            'year.min'     => 'السنة غير صالحة.',
            'year.max'     => 'السنة لا يمكن أن تكون في المستقبل.',

            'description.string' => 'الوصف يجب أن يكون نصاً.',
        ];
    }
}
