<?php

namespace Modules\StudentExits\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentExitLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exit_date'   => ['sometimes', 'date'],
            'exit_time'   => ['sometimes', 'date_format:H:i'],
            'return_time' => ['nullable', 'date_format:H:i'],
            'exit_type'   => ['nullable', 'string', 'max:50'],
            'reason'      => ['nullable', 'string', 'max:255'],
            'note'        => ['nullable', 'string'],
        ];
    }
}
