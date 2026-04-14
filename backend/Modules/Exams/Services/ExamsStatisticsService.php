<?php

namespace Modules\Exams\Services;

use Illuminate\Support\Facades\DB;
use Modules\Exams\Filters\ExamsStatisticsFilter;
use Modules\Exams\Models\Exam;
use Modules\ExamResults\Models\ExamResult;
use Modules\Students\Models\Student;

class ExamsStatisticsService
{
    /**
     * Get students with an average score of 90% or more for a specific month.
     *
     * @param ExamsStatisticsFilter $filter
     * @return \Illuminate\Support\Collection
     */
    public function getMonthlyTopPerformers(ExamsStatisticsFilter $filter)
    {
        $averagePercentageExpression = 'AVG((exam_results.obtained_marks * 100.0) / exams.total_marks)';

        $month = $filter->month;
        $year = $filter->year;

        // 1. Get relevant exam IDs based on filters
        $examQuery = Exam::query();

        if ($year) {
            $examQuery->whereYear('exam_date', $year);
        }

        if ($month) {
            $examQuery->whereMonth('exam_date', $month);
        }

        if ($filter->examTypeId) {
            $examQuery->where('exam_type_id', $filter->examTypeId);
        }

        if ($filter->instituteBranchId) {
            $examQuery->whereHas('batchSubject.batch', function ($q) use ($filter) {
                $q->where('institute_branch_id', $filter->instituteBranchId);
            });
        }

        $examIds = $examQuery->pluck('id');

        if ($examIds->isEmpty()) {
            return collect([]);
        }

        // 2. Aggregate results to find students with average >= 90%
        $topStudentData = ExamResult::query()
            ->whereIn('exam_id', $examIds)
            ->join('exams', 'exam_results.exam_id', '=', 'exams.id')
            ->where('exams.total_marks', '>', 0)
            ->select(
                'exam_results.student_id',
                DB::raw('SUM(exam_results.obtained_marks) as total_obtained'),
                DB::raw('SUM(exams.total_marks) as total_possible'),
                DB::raw("{$averagePercentageExpression} as average_percentage")
            )
            ->groupBy('exam_results.student_id')
            ->havingRaw("{$averagePercentageExpression} >= ?", [90])
            ->orderBy('average_percentage', 'desc')
            ->get();

        if ($topStudentData->isEmpty()) {
            return collect([]);
        }

        // 3. Load Student models to get decrypted names and institute branch info
        $studentIds = $topStudentData->pluck('student_id');
        $students = Student::with(['instituteBranch:id,name'])
            ->whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        // 4. Transform data for the response
        return $topStudentData->map(function ($data) use ($students) {
            $student = $students->get($data->student_id);
            return [
                'student_id'            => $data->student_id,
                'student_name'          => $student ? "{$student->first_name} {$student->last_name}" : 'N/A',
                'institute_branch_name' => $student?->instituteBranch?->name ?? 'N/A',
                'total_obtained'        => (float) $data->total_obtained,
                'total_possible'        => (int) $data->total_possible,
                'average_percentage'    => round((float) $data->average_percentage, 2),
            ];
        });
    }
}
