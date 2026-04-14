<?php

namespace Modules\Students\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Students\Models\Student;
use Carbon\Carbon;

class StudentDataReportController extends Controller
{
    /**
     * توليد تقرير بيانات الطلاب بناءً على الفلاتر
     */
    public function generate(Request $request)
    {
        $request->validate([
            'institute_branch_id' => 'nullable|exists:institute_branches,id',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
            'student_id' => 'nullable|exists:students,id',
            'status_id' => 'nullable|exists:student_statuses,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $query = Student::query()
            ->with(['status', 'bus', 'batchStudents.batch', 'instituteBranch']);

        // Filter by Physical Branch
        if ($request->filled('institute_branch_id')) {
            $query->where('institute_branch_id', $request->institute_branch_id);
        }

        // Filter by Student
        if ($request->filled('student_id')) {
            $query->where('id', $request->student_id);
        }

        // Filter by Status
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter by Enrollment Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('enrollment_date', [$request->start_date, $request->end_date]);
        }

        // Filter by Batches
        if ($request->filled('batch_ids')) {
            $query->whereHas('batchStudents', function ($q) use ($request) {
                $q->whereIn('batch_id', $request->batch_ids);
            });
        }

        $students = $query->get();

        $reportData = $students->map(function ($student) {
            // Map to frontend keys: employeeName, surname, status, busName
            return [
                'id' => $student->id,
                'employeeName' => $student->first_name, // Map to what the user defined
                'surname' => $student->last_name,      // Map to surname
                'status' => $student->status->name ?? '—',
                'busName' => $student->bus->name ?? '—',
                'branch_name' => $student->instituteBranch->name ?? '—',
                'enrollment_date' => $student->enrollment_date ? $student->enrollment_date->format('Y-m-d') : '—',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $reportData,
        ]);
    }
}
