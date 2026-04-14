<?php

namespace Modules\Exams\Filters;

use Illuminate\Http\Request;
use Modules\Shared\Filters\BaseFilter;

class ExamFilter extends BaseFilter
{
    public function __construct(
        public readonly ?int $branchId,
        public readonly ?int $batchId,
        public readonly ?string $gender,
    ) {}

    protected static function make(Request $request): static
    {
        return new static(
            $request->integer('branch_id'),
            $request->integer('batch_id'),
            $request->input('gender'),
        );
    }

    protected static function rules(): array
    {
        return [
            'branch_id' => 'nullable|integer|exists:institute_branches,id',
            'batch_id'  => 'nullable|integer|exists:batches,id',
            'gender'    => 'nullable|in:male,female,mixed',
        ];
    }

    protected static function messages(): array
    {
        return [
            // branch_id
            'branch_id.integer' => 'معرّف الفرع يجب أن يكون رقماً صحيحاً',
            'branch_id.exists'  => 'الفرع المحدد غير موجود في النظام',

            // batch_id
            'batch_id.integer' => 'معرّف الشعبة يجب أن يكون رقماً صحيحاً',
            'batch_id.exists'  => 'الشعبة المحددة غير موجودة في النظام',

            // gender
            'gender.in' => 'قيمة الجنس غير صحيحة. القيم المسموحة: male، female، mixed',
        ];
    }
}
