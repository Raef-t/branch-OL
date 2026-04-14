<?php

namespace Modules\StudentExits\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentExitLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        // يمكنك لاحقاً تقييدها بصلاحيات معيّنة (roles/permissions)
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'exit_date'  => ['nullable', 'date'],
            'exit_time'  => ['nullable', 'date_format:H:i'],
            'return_time'=> ['nullable', 'date_format:H:i'],
            'exit_type'  => ['nullable', 'string', 'max:50'],
            'reason'     => ['nullable', 'string', 'max:255'],
            'note'       => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'حقل الطالب مطلوب.',
            'student_id.exists'   => 'الطالب غير موجود.',
            'exit_date.required'  => 'حقل تاريخ الخروج مطلوب.',
            'exit_time.required'  => 'حقل وقت الخروج مطلوب.',
            'exit_time.date_format' => 'صيغة وقت الخروج يجب أن تكون على شكل HH:MM.',
        ];
    }
}
