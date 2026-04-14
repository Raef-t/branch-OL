<?php
// Updated: 2026-04-13 - Password force change logic refined.

namespace Modules\Students\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Attendances\Models\Attendance;
use Modules\StudentExits\Models\StudentExitLog;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/students/{student}/attendance-log",
     *     summary="سجل حضور وانصراف الطالب",
     *     description="إرجاع سجل الحضور والانصراف للطالب حسب المدة: أسبوع، شهر، أو جميع السجلات.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         required=true,
     *         description="رقم معرف الطالب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="range",
     *         in="query",
     *         required=false,
     *         description="فترة البحث (week | month | all)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"week", "month", "all"},
     *             example="month"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="سجل الحضور",
     *         @OA\JsonContent(
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="range", type="string", example="month"),
     *             @OA\Property(property="count", type="integer", example=15),
     *
     *             @OA\Property(
     *                 property="records",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="date", type="string", example="2025-01-05"),
     *                     @OA\Property(property="check_in", type="string", example="07:55"),
     *                     @OA\Property(property="check_out", type="string", example="13:40"),
     *                     @OA\Property(property="status", type="string", example="present")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="الطالب غير موجود")
     * )
     */
    public function attendanceLog(Request $request, $studentId)
    {
        $range = $request->get('range'); // week | month | all

        // 🗓 تحديد المدة
        if ($range === 'week') {
            $startDate = now()->subWeek()->toDateString();
        } elseif ($range === 'month') {
            $startDate = now()->subMonth()->toDateString();
        } else {
            $startDate = null; // all records
        }

        // 🟦 1) احضار الحضور
        $attendanceQuery = Attendance::where('student_id', $studentId)
            ->orderBy('attendance_date');

        if ($startDate !== null) {
            $attendanceQuery->where('attendance_date', '>=', $startDate);
        }

        $attendances = $attendanceQuery->get([
            'attendance_date',
            'recorded_at',
            'status'
        ]);

        // 🟩 2) احضار الانصراف
        $exitQuery = StudentExitLog::where('student_id', $studentId)
            ->orderBy('exit_date');

        if ($startDate !== null) {
            $exitQuery->where('exit_date', '>=', $startDate);
        }

        $exits = $exitQuery->get([
            'exit_date',
            'exit_time'
        ]);

        // 🟨 دمج السجلات مع ضمان تطابق التاريخ مهما كان التنسيق
        $records = [];

        foreach ($attendances as $attendance) {

            $dateCarbon = Carbon::parse($attendance->attendance_date);

            $date = $dateCarbon->toDateString();
            $dayName = $dateCarbon->translatedFormat('l'); // الأحد، Monday حسب locale

            $checkIn = $attendance->recorded_at
                ? Carbon::parse($attendance->recorded_at)->format("H:i")
                : null;

            $exitRecord = $exits
                ->filter(function ($e) use ($date) {
                    return Carbon::parse($e->exit_date)->toDateString() === $date;
                })
                ->sortByDesc('exit_time')
                ->first();

            $records[] = [
                'date' => $date,
                'day' => $dayName,
                'check_in' => $checkIn,
                'check_out' => $exitRecord
                    ? Carbon::parse($exitRecord->exit_time)->format('H:i')
                    : null,
                'status' => $attendance->status,
            ];
        }


        return response()->json([
            'student_id' => $studentId,
            'range' => $range ?? 'all',
            'count' => count($records),
            'records' => $records,
        ]);
    }
    /**
     * @OA\Put(
     *     path="/api/students/{student}/daily-record",
     *     summary="تعديل سجل اليوم للطالب (الدخول، الخروج، الحالة)",
     *     description="هذا الـ API يقوم بتعديل سجل حضور الطالب لليوم المحدد، بما في ذلك الحالة، وقت الدخول ووقت الخروج. إذا كانت الحالة غياب يتم حذف أي سجل انصراف لذلك اليوم.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date", "status"},
     *
     *             @OA\Property(
     *                 property="date",
     *                 type="string",
     *                 format="date",
     *                 example="2025-11-29",
     *                 description="تاريخ اليوم المراد تعديله"
     *             ),
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"present", "absent"},
     *                 example="present",
     *                 description="حالة الطالب"
     *             ),
     *
     *             @OA\Property(
     *                 property="check_in",
     *                 type="string",
     *                 nullable=true,
     *                 example="07:15",
     *                 description="وقت الدخول بصيغة HH:MM"
     *             ),
     *
     *             @OA\Property(
     *                 property="check_out",
     *                 type="string",
     *                 nullable=true,
     *                 example="13:40",
     *                 description="وقت الخروج بصيغة HH:MM"
     *             ),
     *
     *             @OA\Property(
     *                 property="exit_type",
     *                 type="string",
     *                 nullable=true,
     *                 example="normal",
     *                 description="نوع الانصراف (اختياري)"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم التعديل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="تم تعديل سجل اليوم بنجاح"),
     *
     *             @OA\Property(
     *                 property="attendance",
     *                 type="object",
     *                 example={
     *                     "id": 35,
     *                     "student_id": 15,
     *                     "attendance_date": "2025-11-29",
     *                     "status": "present",
     *                     "recorded_at": "2025-11-29 07:15:00"
     *                 }
     *             ),
     *
     *             @OA\Property(
     *                 property="exit_log",
     *                 type="object",
     *                 nullable=true,
     *                 example={
     *                     "id": 12,
     *                     "student_id": 15,
     *                     "exit_date": "2025-11-29",
     *                     "exit_time": "2025-11-29 13:40:00",
     *                     "exit_type": "normal"
     *                 }
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="لم يتم العثور على السجل أو الطالب"
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من البيانات"
     *     )
     * )
     */

    public function updateDailyRecord(Request $request, $studentId)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'exit_type' => 'nullable|string|max:50',
        ]);

        $date = $request->date;

        // احضار الطالب
        $student = \Modules\Students\Models\Student::findOrFail($studentId);

        //----------------------------------------------------------------------
        // 1) جلب سجل الحضور فقط – دون إنشاء
        //----------------------------------------------------------------------
        $attendance = Attendance::where('student_id', $studentId)
            ->where('attendance_date', $date)
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'لا يوجد سجل حضور لهذا اليوم',
                'status' => false
            ], 404);
        }

        // تعديل بيانات الحضور
        $attendance->status = $request->status;

        if ($request->check_in) {
            $attendance->recorded_at = $date . ' ' . $request->check_in;
        }

        $attendance->save();

        //----------------------------------------------------------------------
        // 2) تعديل أو إضافة سجل الانصراف (هذا مسموح)
        //----------------------------------------------------------------------
        if ($request->status === 'absent') {
            StudentExitLog::where('student_id', $studentId)
                ->whereDate('exit_date', $date)
                ->delete();

            $exit = null;
        } else {

            if ($request->check_out) {

                $exit = StudentExitLog::firstOrNew([
                    'student_id' => $studentId,
                    'exit_date' => $date,
                ]);

                if (isset($exit) && !$exit->exists) {
                    $exit->recorded_by = Auth::id();
                }

                $exit->exit_time = $date . ' ' . $request->check_out;
                $exit->exit_type = $request->exit_type;

                $exit->save();
            } else {
                $exit = null;
            }
        }


        return response()->json([
            'message' => 'تم تعديل سجل اليوم بنجاح',
            'attendance' => $attendance,
            'exit_log' => $exit,
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/students/{studentID}/weekly-attendance",
     *     summary="Get student attendance log (week or specific day)",
     *     description="Returns attendance and exit logs for a student. 
     *                  - By default, returns the current week (Saturday → today). 
     *                  - Optionally, can filter by a specific date using query parameter `date=YYYY-MM-DD`.",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="studentId",
     *         in="path",
     *         description="ID of the student",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Optional specific date to filter attendance (format: YYYY-MM-DD). Overrides the weekly range if provided.",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with attendance records",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="student_id", type="integer", example=123),
     *             @OA\Property(property="attended_today", type="boolean", example=true),
     *             @OA\Property(property="count", type="integer", example=7),
     *             @OA\Property(
     *                 property="records",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="date", type="string", format="date", example="2026-01-01"),
     *                     @OA\Property(property="check_in", type="string", example="08:10", nullable=true),
     *                     @OA\Property(property="check_out", type="string", example="13:30", nullable=true),
     *                     @OA\Property(property="status", type="string", example="present")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found or no records",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student not found")
     *         )
     *     )
     * )
     */
    public function attendanceLogWeekAndDay(Request $request, $studentId)
    {
        $specificDate = $request->get('date'); // YYYY-MM-DD

        // 🗓 تحديد المدة
        if ($specificDate) {
            // إذا تم تمرير تاريخ محدد، نركز على هذا اليوم فقط
            $startDate = $specificDate;
            $endDate = $specificDate;
        } else {
            // الأسبوع الحالي: من السبت حتى اليوم
            $today = now();

            // تحديد بداية الأسبوع
            if ($today->isSaturday()) {
                $startDate = $today->toDateString(); // اليوم هو السبت → بداية الأسبوع اليوم
            } else {
                // غير السبت → نرجع إلى السبت السابق
                $startDate = $today->copy()->subDays($today->dayOfWeek + 1 % 7)->toDateString();
                // dayOfWeek: 0=Sun, 6=Sat → السبت السابق
            }
            // نهاية الأسبوع هي اليوم الحالي
            $endDate = $today->toDateString();
        }
        // 🟦 جلب الحضور
        $attendanceQuery = Attendance::where('student_id', $studentId)
            ->orderBy('attendance_date')
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        $attendances = $attendanceQuery->get([
            'attendance_date',
            'recorded_at',
            'status'
        ]);

        // 🟩 جلب الانصراف
        $exitQuery = StudentExitLog::where('student_id', $studentId)
            ->orderBy('exit_date')
            ->whereBetween('exit_date', [$startDate, $endDate]);

        $exits = $exitQuery->get([
            'exit_date',
            'exit_time'
        ]);

        // 🟨 بناء سجل كامل مع الحالة اليومية
        $records = [];

        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $day) {
            $date = $day->format('Y-m-d');

            $attendance = $attendances->first(fn($a) => Carbon::parse($a->attendance_date)->toDateString() === $date);

            $checkIn = $attendance && $attendance->recorded_at
                ? Carbon::parse($attendance->recorded_at)->format("H:i")
                : null;

            $exitRecord = $exits
                ->filter(fn($e) => Carbon::parse($e->exit_date)->toDateString() === $date)
                ->sortByDesc('exit_time')
                ->first();

            $checkOut = $exitRecord
                ? Carbon::parse($exitRecord->exit_time)->format('H:i')
                : null;

            $records[] = [
                'date' => $date,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => $attendance ? $attendance->status : 'absent',
            ];
        }

        // 🔥 حالة الطالب اليوم
        $todayAttendance = $attendances->firstWhere('attendance_date', now()->toDateString());

        return response()->json([
            'student_id' => $studentId,
            'attended_today' => $todayAttendance ? true : false,
            'count' => count($records),
            'records' => $records,
        ]);
    }
}
