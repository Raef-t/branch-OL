<?php // Modules/Exams/Filters/BatchAttendanceVerificationFilter.php

namespace Modules\Exams\Filters;

use Illuminate\Http\Request;
use Modules\Shared\Filters\BaseFilter as FiltersBaseFilter;

class BatchAttendanceVerificationFilter extends FiltersBaseFilter
{
    public function __construct(
        public readonly ?int $batchId,
        public readonly ?int $studentId,
        public readonly ?int $subjectId,
        public readonly ?int $instituteBranchId,
    ) {}

    protected static function make(Request $request): static
    {
        return new self(
            batchId: $request->integer('batch_id'),
            studentId: $request->integer('student_id'),
            subjectId: $request->integer('subject_id'),
            instituteBranchId: $request->integer('institute_branch_id'),
        );
    }

    protected static function rules(): array
    {
        return [
            'batch_id'              => 'nullable|integer|exists:batches,id',
            'student_id'            => 'nullable|integer|exists:students,id',
            'subject_id'            => 'nullable|integer|exists:subjects,id',
            'institute_branch_id'   => 'nullable|integer|exists:institute_branches,id',
        ];
    }

    protected static function messages(): array
    {
        return [
            'batch_id.exists'              => 'الدورة المحددة غير موجودة.',
            'student_id.exists'            => 'الطالب المحدد غير موجود.',
            'subject_id.exists'            => 'المادة المحددة غير موجودة.',
            'institute_branch_id.exists'   => 'الموقع الجغرافي غير موجود.',
        ];
    }
}
