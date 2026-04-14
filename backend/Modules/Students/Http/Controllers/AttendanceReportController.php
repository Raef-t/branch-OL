<?php

namespace Modules\Students\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Students\Models\Student;
use Modules\Batches\Models\Batch;
use Modules\Attendances\Models\Attendance;
use Modules\StudentExits\Models\StudentExitLog;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\Shared\Traits\SuccessResponseTrait;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    use SuccessResponseTrait;

    /**
     * Get students filtered by institute branch and optionally by specific batches.
     * Used to refresh the student dropdown in the reporting UI.
     */
    public function getStudentsByBatches(Request $request)
    {
        $request->validate([
            'institute_branch_id' => 'nullable|exists:institute_branches,id',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
        ]);

        $query = Student::query();

        if ($request->filled('institute_branch_id')) {
            $query->where('institute_branch_id', $request->institute_branch_id);
        }

        // Only return students from ACTIVE batches by default
        $query->whereHas('batchStudents.batch', function ($q) use ($request) {
            $q->where('batches.is_hidden', false)->where('batches.is_archived', false);
            if ($request->filled('batch_ids')) {
                $q->whereIn('batches.id', $request->batch_ids);
            }
        });

        $students = $query->orderBy('first_name_hash')->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'full_name' => $s->full_name,
            ];
        });

        return $this->successResponse($students, 'تم جلب قائمة الطلاب بنجاح');
    }

    /**
     * Generate the attendance report data.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'institute_branch_id' => 'nullable|exists:institute_branches,id',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
            'student_id' => 'nullable|exists:students,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // 1. Identify base students for the report (from ALL branches if branch_id is null)
        $studentQuery = Student::query();

        if ($request->filled('institute_branch_id')) {
            $studentQuery->where('institute_branch_id', $request->institute_branch_id);
        }

        if ($request->filled('student_id')) {
            $studentQuery->where('id', $request->student_id);
        }

        // Mandatory: Only students in ACTIVE batches
        $studentQuery->whereHas('batchStudents.batch', function ($q) use ($request) {
            $q->where('batches.is_hidden', false)->where('batches.is_archived', false);
            if ($request->filled('batch_ids')) {
                $q->whereIn('batches.id', $request->batch_ids);
            }
        });

        $studentIds = $studentQuery->pluck('id');
        $students = $studentQuery->with('batchStudents.batch')->get()->keyBy('id');

        // 2. Fetch Attendance (Check-in / Status)
        $attendances = Attendance::whereIn('student_id', $studentIds)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        // 3. Fetch Exit Logs (Check-out)
        $exits = StudentExitLog::whereIn('student_id', $studentIds)
            ->whereBetween('exit_date', [$startDate, $endDate])
            ->get();

        // 4. Merge and Group Data
        $reportData = [];

        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $dateObj) {
            $date = $dateObj->format('Y-m-d');

            foreach ($studentIds as $sId) {
                // Fix: Ensure we compare only the date portion or use Carbon
                $attRec = $attendances->where('student_id', $sId)->first(function($att) use ($date) {
                    return Carbon::parse($att->attendance_date)->format('Y-m-d') === $date;
                });
                
                $exitRec = $exits->where('student_id', $sId)->first(function($ex) use ($date) {
                    return Carbon::parse($ex->exit_date)->format('Y-m-d') === $date;
                });

                // Skip if no record exists for that day (efficiency for large data)
                if (!$attRec && !$exitRec) continue;

                $student = $students->get($sId);
                
                $batchNames = $student->batchStudents->map(function($bs) {
                    return $bs->batch->name ?? '—';
                })->unique()->implode(', ');

                $daysAr = [
                    'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء',
                    'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت', 'Sunday' => 'الأحد',
                ];

                $statusAr = [
                    'present' => 'حاضر', 'absent' => 'غائب', 'leave' => 'مجاز', 'late' => 'متأخر', 'unknown' => '—',
                ];

                $reportData[] = [
                    'student_id' => $sId,
                    'student_name' => $student->full_name,
                    'batch_name' => $batchNames,
                    'date' => $date,
                    'day' => $daysAr[$dateObj->format('l')] ?? $dateObj->format('l'),
                    'status' => $statusAr[$attRec->status ?? 'unknown'] ?? ($attRec->status ?? '—'),
                    'check_in' => $attRec && $attRec->recorded_at ? Carbon::parse($attRec->recorded_at)->format('H:i') : '—',
                    'check_out' => $exitRec && $exitRec->exit_time ? Carbon::parse($exitRec->exit_time)->format('H:i') : '—',
                ];
            }
        }

        return $this->successResponse($reportData, 'تم توليد التقرير بنجاح');
    }
}
