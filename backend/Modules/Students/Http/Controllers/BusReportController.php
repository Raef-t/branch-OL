<?php

namespace Modules\Students\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Students\Models\Student;
use Modules\Buses\Models\Bus;

class BusReportController extends Controller
{
    /**
     * توليد تقرير الباصات بناءً على الفلاتر
     */
    public function generate(Request $request)
    {
        $request->validate([
            'institute_branch_id' => 'nullable|exists:institute_branches,id',
            'bus_ids' => 'nullable|array',
            'bus_ids.*' => 'exists:buses,id',
            'student_id' => 'nullable|exists:students,id',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
        ]);

        $query = Student::query()
            ->with(['bus', 'status', 'batchStudents.batch', 'instituteBranch']);

        // Filter by Physical Branch
        if ($request->filled('institute_branch_id')) {
            $query->where('institute_branch_id', $request->institute_branch_id);
        }

        // Filter by Buses
        if ($request->filled('bus_ids')) {
            $query->whereIn('bus_id', $request->bus_ids);
        }

        // Filter by Student
        if ($request->filled('student_id')) {
            $query->where('id', $request->student_id);
        }

        // Filter by Batches
        if ($request->filled('batch_ids')) {
            $query->whereHas('batchStudents', function ($q) use ($request) {
                $q->whereIn('batch_id', $request->batch_ids);
            });
        }

        $students = $query->get();

        $reportData = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'employeeName' => $student->first_name,
                'surname' => $student->last_name,
                'status' => $student->status->name ?? '—',
                'busName' => $student->bus->name ?? '—',
                'busNumber' => $student->bus->bus_number ?? ($student->bus->id ?? '—'),
                'branch_name' => $student->instituteBranch->name ?? '—',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $reportData,
        ]);
    }
}
