<?php

namespace Modules\Students\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Students\Models\Student;
use Modules\Exams\Models\Exam;
use Modules\ExamResults\Models\ExamResult;
use Modules\BatchStudents\Models\BatchStudent;
use Carbon\Carbon;

class ExamReportController extends Controller
{
    /**
     * توليد تقرير المذاكرات بناءً على الفلاتر
     */
    public function generate(Request $request)
    {
        $request->validate([
            'institute_branch_id' => 'nullable|exists:institute_branches,id',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
            'student_id' => 'nullable|exists:students,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $batchIds = $request->batch_ids;
        $studentId = $request->student_id;
        $branchId = $request->institute_branch_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // 1. Get Exams in the specified range and batches
        $examQuery = Exam::query()
            ->with(['batchSubject.batch', 'batchSubject.subject']);

        if ($startDate && $endDate) {
            $examQuery->whereBetween('exam_date', [$startDate, $endDate]);
        }

        if ($batchIds) {
            $examQuery->whereHas('batchSubject', function ($q) use ($batchIds) {
                $q->whereIn('batch_id', $batchIds);
            });
        } elseif ($branchId) {
            $examQuery->whereHas('batchSubject.batch', function ($q) use ($branchId) {
                $q->where('institute_branch_id', $branchId);
            });
        }

        $exams = $examQuery->get();
        $examIds = $exams->pluck('id');

        // 2. Get Students (Filtered)
        $studentQuery = Student::query();
        if ($studentId) {
            $studentQuery->where('id', $studentId);
        } elseif ($batchIds) {
            $studentQuery->whereHas('batchStudents', function ($q) use ($batchIds) {
                $q->whereIn('batch_id', $batchIds);
            });
        } elseif ($branchId) {
            $studentQuery->where('institute_branch_id', $branchId);
        }

        $students = $studentQuery->select('id', 'first_name', 'last_name', 'institute_branch_id')->get();
        $studentIds = $students->pluck('id');

        // 3. Get Results for these exams and students
        $results = ExamResult::whereIn('exam_id', $examIds)
            ->whereIn('student_id', $studentIds)
            ->get();

        // 4. Build Report Rows
        $reportData = [];
        
        foreach ($exams as $exam) {
            $currentBatchId = $exam->batchSubject->batch_id;
            
            // Find students who should have taken this exam (those in the batch)
            $studentsInBatch = BatchStudent::where('batch_id', $currentBatchId)
                ->whereIn('student_id', $studentIds)
                ->pluck('student_id');

            foreach ($studentsInBatch as $sId) {
                $studentRec = $students->find($sId);
                if (!$studentRec) continue;

                $res = $results->where('exam_id', $exam->id)->where('student_id', $sId)->first();
                $obtained = $res ? (float)$res->obtained_marks : null;
                $total = (float)$exam->total_marks;

                // Format as integer if no decimals, else keep 1 decimal
                $obtainedStr = ($obtained !== null) ? (floor($obtained) == $obtained ? (int)$obtained : number_format($obtained, 1)) : "—";
                $totalStr = (floor($total) == $total ? (int)$total : number_format($total, 1));

                $reportData[] = [
                    'id' => $exam->id . '-' . $sId,
                    'studentId' => $sId,
                    'employeeName' => $studentRec->first_name,
                    'surname' => $studentRec->last_name,
                    'examName' => $exam->name . ' (' . ($exam->batchSubject->subject->name ?? '—') . ')',
                    // Format shows as Mark / Total
                    'grade' => "{$obtainedStr} / {$totalStr}",
                    'status' => $res ? "مستلمة" : "غير مستلمة",
                    'date' => $exam->exam_date->format('Y-m-d'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $reportData,
            'stats' => [
                'received' => collect($reportData)->where('status', 'مستلمة')->count(),
                'notReceived' => collect($reportData)->where('status', 'غير مستلمة')->count(),
            ]
        ]);
    }
}
