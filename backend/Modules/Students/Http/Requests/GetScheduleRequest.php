<?php

declare(strict_types=1);

namespace Modules\Students\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class GetScheduleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('is_default')) {
            $this->merge([
                'is_default' => filter_var($this->query('is_default'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    public function rules(): array
    {
        $type = $this->query('type');

        $rules = [
            'type' => ['required', 'in:student,batch,location'],
            'day' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ];

        // قواعد شرطية حسب النوع
        if ($type === 'location') {
            $rules['institute_branch_id'] = ['required', 'integer'];
            $rules['id'] = ['nullable'];
        } else {
            $rules['id'] = ['required', 'integer'];
            $rules['institute_branch_id'] = ['nullable', 'integer'];
        }

        return $rules;
    }
}
