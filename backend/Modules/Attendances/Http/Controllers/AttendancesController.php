<?php

namespace Modules\Attendances\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Attendances\Models\Attendance;
use Modules\Attendances\Http\Requests\StoreAttendanceRequest;
use Modules\Attendances\Http\Requests\UpdateAttendanceRequest;
use Modules\Attendances\Http\Resources\AttendanceResource;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Students\Models\Student;

class AttendancesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/attendance",
     *     summary="قائمة جميع سجلات الحضور",
     *     tags={"Attendance"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع سجلات الحضور بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع سجلات الحضور بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                     @OA\Property(property="student_id", type="integer", example=1),
     *                     @OA\Property(property="batch_id", type="integer", example=1),
     *                     @OA\Property(property="attendance_date", type="string", format="date", example="2025-09-29"),
     *                     @OA\Property(property="status", type="string", example="present"),
     *                     @OA\Property(property="recorded_by", type="integer", example=1),
     *                     @OA\Property(property="device_id", type="string", example="DOOR_MAIN_01", nullable=true),
     *                     @OA\Property(property="recorded_at", type="string", format="date-time", example="2025-09-29T12:20:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد سجلات حضور",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي سجلات حضور مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $attendance = Attendance::all();
        if ($attendance->isEmpty()) {
            return $this->error('لا يوجد أي سجلات حضور مسجلة حالياً', 404);
        }
        return $this->successResponse(
            AttendanceResource::collection($attendance),
            'تم جلب جميع سجلات الحضور بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/attendance/latest",
     *     summary="جلب سجلات الحضور لآخر أسبوعين فقط",
     *     description="يقوم هذا المسار بإرجاع سجلات الحضور التي تمت خلال آخر 14 يوماً.",
     *     tags={"Attendance"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب سجلات الحضور للأسبوعين الأخيرين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب سجلات الحضور للأسبوعين الأخيرين بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function latest()
    {
        $twoWeeksAgo = now()->subDays(14);
        $attendance = Attendance::where('attendance_date', '>=', $twoWeeksAgo)
            ->orderBy('attendance_date', 'desc')
            ->get();

        if ($attendance->isEmpty()) {
            return $this->error('لا يوجد أي سجلات حضور خلال آخر أسبوعين', 404);
        }

        return $this->successResponse(
            AttendanceResource::collection($attendance),
            'تم جلب سجلات الحضور للأسبوعين الأخيرين بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/attendance",
     *     summary="إضافة سجل حضور جديد",
     *     tags={"Attendance"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"institute_branch_id","student_id","batch_id","attendance_date","status","recorded_by"},
     *             @OA\Property(property="institute_branch_id", type="integer", example=1),
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="batch_id", type="integer", example=1),
     *             @OA\Property(property="attendance_date", type="string", format="date", example="2025-09-29"),
     *             @OA\Property(property="status", type="string", example="present"),
     *             @OA\Property(property="recorded_by", type="integer", example=1),
     *             @OA\Property(property="device_id", type="string", example="DOOR_MAIN_01", nullable=true),
     *             @OA\Property(property="recorded_at", type="string", format="date-time", example="2025-09-29T12:20:00Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء سجل الحضور بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء سجل الحضور بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                 @OA\Property(property="student_id", type="integer", example=1),
     *                 @OA\Property(property="batch_id", type="integer", example=1),
     *                 @OA\Property(property="attendance_date", type="string", format="date", example="2025-09-29"),
     *                 @OA\Property(property="status", type="string", example="present"),
     *                 @OA\Property(property="recorded_by", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="string", example="DOOR_MAIN_01", nullable=true),
     *                 @OA\Property(property="recorded_at", type="string", format="date-time", example="2025-09-29T12:20:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreAttendanceRequest $request)
    {
        $attendance = Attendance::create($request->validated());

        return $this->successResponse(
            new AttendanceResource($attendance),
            'تم إنشاء سجل الحضور بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/attendance/{id}",
     *     summary="عرض تفاصيل سجل حضور محدد",
     *     tags={"Attendance"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف سجل الحضور",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات سجل الحضور بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات سجل الحضور بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                 @OA\Property(property="student_id", type="integer", example=1),
     *                 @OA\Property(property="batch_id", type="integer", example=1),
     *                 @OA\Property(property="attendance_date", type="string", format="date", example="2025-09-29"),
     *                 @OA\Property(property="status", type="string", example="present"),
     *                 @OA\Property(property="recorded_by", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="string", example="DOOR_MAIN_01", nullable=true),
     *                 @OA\Property(property="recorded_at", type="string", format="date-time", example="2025-09-29T12:20:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سجل الحضور غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="سجل الحضور غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return $this->error('سجل الحضور غير موجود', 404);
        }

        return $this->successResponse(
            new AttendanceResource($attendance),
            'تم جلب بيانات سجل الحضور بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/attendance/{id}",
     *     summary="تحديث سجل حضور",
     *     tags={"Attendance"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف سجل الحضور",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="institute_branch_id", type="integer", example=2),
     *             @OA\Property(property="student_id", type="integer", example=2),
     *             @OA\Property(property="batch_id", type="integer", example=2),
     *             @OA\Property(property="attendance_date", type="string", format="date", example="2025-09-30"),
     *             @OA\Property(property="status", type="string", example="absent"),
     *             @OA\Property(property="recorded_by", type="integer", example=2),
     *             @OA\Property(property="device_id", type="string", example="DOOR_MAIN_02", nullable=true),
     *             @OA\Property(property="recorded_at", type="string", format="date-time", example="2025-09-29T12:25:00Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث سجل الحضور بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث سجل الحضور بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=2),
     *                 @OA\Property(property="student_id", type="integer", example=2),
     *                 @OA\Property(property="batch_id", type="integer", example=2),
     *                 @OA\Property(property="attendance_date", type="string", format="date", example="2025-09-30"),
     *                 @OA\Property(property="status", type="string", example="absent"),
     *                 @OA\Property(property="recorded_by", type="integer", example=2),
     *                 @OA\Property(property="device_id", type="string", example="DOOR_MAIN_02", nullable=true),
     *                 @OA\Property(property="recorded_at", type="string", format="date-time", example="2025-09-29T12:25:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سجل الحضور غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="سجل الحضور غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateAttendanceRequest $request, $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return $this->error('سجل الحضور غير موجود', 404);
        }

        $attendance->update($request->validated());

        return $this->successResponse(
            new AttendanceResource($attendance),
            'تم تحديث سجل الحضور بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/attendance/{id}",
     *     summary="حذف سجل حضور",
     *     tags={"Attendance"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف سجل الحضور",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف سجل الحضور بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف سجل الحضور بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سجل الحضور غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="سجل الحضور غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return $this->error('سجل الحضور غير موجود', 404);
        }

        $attendance->delete();

        return $this->successResponse(
            null,
            'تم حذف سجل الحضور بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/attendance/manual",
     *     summary="تسجيل حضور الطالب يدويًا بواسطة الموظف",
     *     description="يُستخدم هذا المسار لتسجيل حضور الطالب يدويًا (في حال تعطل جهاز الباب أو الحاجة للتسجيل اليدوي). يستنتج النظام بيانات الفرع والدورة من معلومات الطالب تلقائيًا.",
     *     tags={"Attendances"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="البيانات المطلوبة لتسجيل الحضور يدويًا",
     *         @OA\JsonContent(
     *             required={"student_id"},
     *             @OA\Property(
     *                 property="student_id",
     *                 type="integer",
     *                 example=123,
     *                 description="معرف الطالب الذي سيتم تسجيل حضوره"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"present","absent","late"},
     *                 example="present",
     *                 description="حالة الحضور (افتراضي: present)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تسجيل حضور الطالب يدويًا بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="تم تسجيل حضور الطالب أحمد محمد بنجاح."
     *             ),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود أو لا يملك دفعة مرتبطة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="لم يتم العثور على دفعة مرتبطة بالطالب أحمد محمد."
     *             ),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في إدخال البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل التحقق من صحة البيانات."),
     *             @OA\Property(property="errors", type="object",
     *                 example={"student_id": {"الطالب غير موجود"}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="فشل المصادقة (رمز الدخول غير صالح أو منتهي)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function manual(Request $request)
    {
        try {
            // ✅ التحقق من صحة الطلب
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'status'     => 'nullable|in:present,absent,late',
            ], [
                'student_id.required' => 'معرف الطالب مطلوب.',
                'student_id.exists'   => 'الطالب المحدد غير موجود.',
                'status.in'           => 'قيمة حالة الحضور غير صالحة. يجب أن تكون: present أو absent أو late.',
            ]);

            $user = Auth::user();

            // 🔍 البحث عن الطالب مع علاقاته الضرورية
            $student = Student::with('latestBatchStudent')->find($validated['student_id']);

            if (! $student) {
                return $this->error('الطالب غير موجود.', 404);
            }

            // ⚠️ التحقق من وجود دفعة مرتبطة
            $batchId = $student->latestBatchStudent?->batch_id;

            if (! $batchId) {
                return $this->error('لم يتم العثور على دفعة مرتبطة بالطالب ' . $student->name . '.', 404);
            }

            // 🕓 تسجيل الحضور
            Attendance::create([
                'institute_branch_id' => $student->institute_branch_id,
                'student_id'          => $student->id,
                'batch_id'            => $batchId,
                'attendance_date'     => Carbon::today(),
                'status'              => $validated['status'] ?? 'present',
                'recorded_by'         => $user->id,
                'device_id'           => null,
                'recorded_at'         => now(),
            ]);

            return $this->successResponse(
                null,
                'تم تسجيل حضور الطالب ' . $student->first_name . ' ' . $student->last_name . ' بنجاح.',
                200
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل التحقق من صحة البيانات.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            // 🛠️ أي خطأ غير متوقع
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ غير متوقع أثناء تسجيل الحضور.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/attendance/group",
     *     summary="تسجيل حضور جماعي لدفعة معينة",
     *     description="يسمح هذا المسار بتسجيل حضور مجموعة من الطلاب ضمن دفعة واحدة بشكل جماعي. يتم التحقق من انتماء كل طالب للدفعة، ومن عدم وجود سجل حضور مسبق في نفس اليوم.",
     *     tags={"Attendance"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"batch_id","students"},
     *             @OA\Property(property="batch_id", type="integer", example=12),
     *             @OA\Property(property="date", type="string", format="date", example="2025-11-27"),
     *             @OA\Property(
     *                 property="students",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="student_id", type="integer", example=101),
     *                     @OA\Property(property="status", type="string", enum={"present","absent","late"}, example="present")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تسجيل الحضور الجماعي بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تسجيل الحضور الجماعي بنجاح."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="created", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(
     *                     property="skipped",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="student_id", type="integer", example=101),
     *                         @OA\Property(property="reason", type="string", example="الطالب لا ينتمي إلى هذه الدفعة")
     *                     )
     *                 ),
     *                 @OA\Property(property="count_created", type="integer", example=18),
     *                 @OA\Property(property="count_skipped", type="integer", example=3)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في البيانات المرسلة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل التحقق من البيانات.")
     *         )
     *     )
     * )
     */
    public function groupAttendance(Request $request)
    {
        // 🎯 التحقق من صحة البيانات
        $validated = $request->validate([
            'batch_id'  => 'required|exists:batches,id',
            'date'      => 'nullable|date',
            'students'  => 'required|array|min:1',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.status'     => 'required|in:present,absent,late',
        ], [
            'students.required' => 'قائمة الطلاب مطلوبة.',
            'students.*.student_id.exists' => 'أحد الطلاب غير موجود.',
        ]);

        $user = Auth::user();
        $date = $validated['date'] ?? Carbon::today()->toDateString();

        $created = [];
        $skipped = [];

        foreach ($validated['students'] as $record) {

            $studentId = $record['student_id'];

            // ⛔ 1) التحقق من انتماء الطالب للدفعة
            $belongs = BatchStudent::where('student_id', $studentId)
                ->where('batch_id', $validated['batch_id'])
                ->exists();

            if (! $belongs) {
                $skipped[] = [
                    'student_id' => $studentId,
                    'reason' => 'الطالب لا ينتمي إلى هذه الدفعة'
                ];
                continue;
            }

            // ⛔ 2) التحقق من عدم وجود حضور سابق لليوم
            $exists = Attendance::where('student_id', $studentId)
                ->where('attendance_date', $date)
                ->exists();

            if ($exists) {
                $skipped[] = [
                    'student_id' => $studentId,
                    'reason' => 'تم تسجيل الحضور مسبقاً'
                ];
                continue;
            }

            // 🔹 جلب بيانات الطالب لتحديد الفرع
            $student = Student::find($studentId);

            // ✅ 3) تسجيل الحضور
            Attendance::create([
                'institute_branch_id' => $student->institute_branch_id,
                'student_id'          => $studentId,
                'batch_id'            => $validated['batch_id'],
                'attendance_date'     => $date,
                'status'              => $record['status'],
                'recorded_by'         => $user->id,
                'device_id'           => null,
                'recorded_at'         => now(),
            ]);

            $created[] = $studentId;
        }

        return $this->successResponse([
            'created'       => $created,
            'skipped'       => $skipped,
            'count_created' => count($created),
            'count_skipped' => count($skipped),
        ], 'تم تسجيل الحضور الجماعي بنجاح.');
    }
}
