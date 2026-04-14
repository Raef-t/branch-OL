<?php

namespace Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Attendances\Models\Attendance;
use Modules\Students\Models\Student;
use Modules\Shared\Traits\SuccessResponseTrait;

class ReportsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/reports/students",
     *     operationId="studentsReport",
     *     tags={"Reports"},
     *     summary="Get Students Financial Report",
     *     description="Returns a filtered report of students including personal information, attendance start date, enrollment date, and discount percentage from the latest active enrollment contract.",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="batch_ids[]",
     *         in="query",
     *         required=false,
     *         description="Filter by multiple batch IDs",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer")
     *         ),
     *         style="form",
     *         explode=true,
     *         example={1,2,3}
     *     ),
     *
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         required=false,
     *         description="Filter by specific student ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=5
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         required=false,
     *         description="Filter start date (start_attendance_date)",
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2025-01-01"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         required=false,
     *         description="Filter end date (start_attendance_date)",
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2025-12-31"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Students report retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب تقرير الطلاب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="student_id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="محمد"),
     *                     @OA\Property(property="last_name", type="string", example="أحمد"),
     *                     @OA\Property(property="enrollment_date", type="string", format="date", example="2025-01-10"),
     *                     @OA\Property(property="start_attendance_date", type="string", format="date", example="2025-01-15"),
     *                     @OA\Property(property="discount_percentage", type="number", format="float", example=10),
     *                     @OA\Property(property="discount_amount", type="number", format="float", example=100.00)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Sanctum token is missing or invalid",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="No matching students found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد طلاب مطابقين لمعايير البحث")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $batchIds  = $request->input('batch_ids');           // array أو null
        $studentId = $request->input('student_id');          // single id أو null
        $dateFrom  = $request->input('date_from');           // Y-m-d
        $dateTo    = $request->input('date_to');             // Y-m-d

        $query = Student::query()
            ->with([
                'latestActiveEnrollmentContract',
                'latestBatchStudent',
            ])
            ->when($studentId, fn($q) => $q->where('id', $studentId))
            ->when($batchIds, function ($q) use ($batchIds) {
                // دعم array أو comma separated string
                $ids = is_array($batchIds) ? $batchIds : explode(',', $batchIds);
                $q->whereHas('batchStudents', fn($sub) => $sub->whereIn('batch_id', $ids));
            })
            ->when($dateFrom && $dateTo, fn($q) => 
                $q->whereBetween('start_attendance_date', [$dateFrom, $dateTo])
            );

        $students = $query->get();

        if ($students->isEmpty()) {
            return $this->error('لا يوجد طلاب مطابقين لمعايير البحث', 404);
        }

        $data = $students->map(function ($student) {
            $contract = $student->latestActiveEnrollmentContract;

            return [
                'student_id'            => $student->id,
                'first_name'            => $student->first_name,
                'last_name'             => $student->last_name,
                'enrollment_date'       => $student->enrollment_date?->toDateString(),
                'start_attendance_date' => $student->start_attendance_date?->toDateString(),
                'discount_percentage'   => $contract?->discount_percentage ?? 0,   // 0 بدل null إذا أردت
                'discount_amount'       => $contract?->discount_amount ?? 0,

                // اختياري – إذا أردت إرجاع رقم الشعبة الأحدث
                // 'current_batch_id'   => $student->latestBatchStudent?->batch_id,
            ];
        });

        return $this->successResponse(
            $data,
            'تم جلب تقرير الطلاب بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/reports/students/attendanceReport",
     *     operationId="attendanceReport",
     *     tags={"Reports"},
     *     summary="تقرير الدوام",
     *     description="إرجاع تقرير الدوام مع إمكانية الفلترة حسب الطالب أو الدفعات أو التاريخ",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         description="معرف الطالب",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *
     *     @OA\Parameter(
     *         name="batch_ids[]",
     *         in="query",
     *         description="معرفات الدفعات",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer")
     *         ),
     *         style="form",
     *         explode=true
     *     ),
     *
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="تاريخ البداية",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="تاريخ النهاية",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-31")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب تقرير الدوام بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب تقرير الدوام بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="day_name", type="string", example="الاثنين"),
     *                     @OA\Property(property="date", type="string", example="2025-01-15"),
     *                     @OA\Property(property="student_name", type="string", example="أحمد محمد"),
     *                     @OA\Property(property="status", type="string", example="موجود"),
     *                     @OA\Property(property="check_in_time", type="string", example="08:15"),
     *                     @OA\Property(property="check_out_time", type="string", example="12:30")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد بيانات"
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح"
     *     )
     * )
     */
    public function attendanceReport(Request $request)
    {
        $studentId = $request->input('student_id');
        $batchIds  = $request->input('batch_ids', []);
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');

        $attendances = Attendance::query()
            ->with([
                'student.studentExitLogs' // نحمل سجلات الخروج مرة واحدة
            ])
            ->when($studentId, fn($q) =>
                $q->where('student_id', $studentId)
            )
            ->when(!empty($batchIds), fn($q) =>
                $q->whereIn('batch_id', $batchIds)
            )
            ->when($dateFrom && $dateTo, fn($q) =>
                $q->whereBetween('attendance_date', [$dateFrom, $dateTo])
            )
            ->orderByDesc('attendance_date')
            ->get();

        if ($attendances->isEmpty()) {
            return $this->error('لا يوجد سجلات دوام مطابقة لمعايير البحث', 404);
        }

        Carbon::setLocale('ar');

        $data = $attendances->map(function ($attendance) {

            $date = $attendance->attendance_date;

            // نبحث عن سجل الخروج المطابق لنفس اليوم
            $exitLog = $attendance->student?->studentExitLogs
                ?->firstWhere('exit_date', $date);
  
            return [
                'day_name'       => $date?->translatedFormat('l'),
                'date'           => $date?->toDateString(),
                'student_name'   => $attendance->student?->full_name,
                'status'         => $attendance->status === 'present'
                                        ? 'موجود'
                                        : 'غائب',
                'check_in_time'  => $attendance->recorded_at?->format('H:i'),
                'check_out_time' => $exitLog?->exit_time?->format('H:i'),
            ];
        });

        return $this->successResponse(
            $data,
            'تم جلب تقرير الدوام بنجاح',
            200
        );
    }
}
