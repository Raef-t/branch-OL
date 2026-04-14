<?php

namespace Modules\Exams\Filters;

use Illuminate\Http\Request;
use Modules\Shared\Filters\BaseFilter;

class ExamsStatisticsFilter extends BaseFilter
{
    public function __construct(
        public readonly ?int $month,
        public readonly ?int $year,
        public readonly ?int $examTypeId,
        public readonly ?int $instituteBranchId,
    ) {}

    protected static function make(Request $request): static
    {
        $month = $request->has('month') ? $request->integer('month') : null;
        $year = $request->has('year') ? $request->integer('year') : null;

        // If neither is sent, default to current month and year
        if (!$month && !$year) {
            $month = now()->month;
            $year = now()->year;
        }

        return new static(
            $month,
            $year,
            $request->has('exam_type_id') ? $request->integer('exam_type_id') : null,
            $request->has('institute_branch_id') ? $request->integer('institute_branch_id') : null,
        );
    }

    protected static function rules(): array
    {
        return [
            'month'               => 'nullable|integer|between:1,12',
            'year'                => 'nullable|integer|min:2020',
            'exam_type_id'        => 'nullable|integer|exists:exam_types,id',
            'institute_branch_id' => 'nullable|integer|exists:institute_branches,id',
        ];
    }

    protected static function messages(): array
    {
        return [
            'month.between'               => 'الشهر يجب أن يكون بين 1 و 12',
            'year.min'                     => 'السنة غير صالحة',
            'exam_type_id.exists'          => 'نوع الامتحان المحدد غير موجود',
            'institute_branch_id.exists'   => 'فرع المعهد المحدد غير موجود',
        ];
    }
}
