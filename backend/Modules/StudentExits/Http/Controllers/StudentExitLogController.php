<?php

namespace Modules\StudentExits\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\StudentExits\Http\Requests\StoreStudentExitLogRequest;
use Modules\StudentExits\Http\Requests\UpdateStudentExitLogRequest;
use Modules\StudentExits\Http\Resources\StudentExitLogResource;
use Modules\StudentExits\Models\StudentExitLog;
use Modules\StudentExits\Services\StudentExitLogService;
use Modules\Shared\Traits\SuccessResponseTrait;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Student Exit Logs",
 *     description="إدارة سجلات خروج الطلاب"
 * )
 */
class StudentExitLogController extends Controller
{
    use SuccessResponseTrait;

    protected StudentExitLogService $service;

    public function __construct(StudentExitLogService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/student-exits",
     *     summary="قائمة سجلات الخروج",
     *     tags={"Student Exit Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         description="رقم الطالب",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="تاريخ الخروج (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/StudentExitLogResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = StudentExitLog::with(['student.user', 'recorder']);

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->date) {
            $query->where('exit_date', $request->date);
        }

        $logs = $query->orderBy('exit_date', 'desc')->paginate(30);

        return $this->successResponse(
            StudentExitLogResource::collection($logs),
            'قائمة سجلات الخروج'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/student-exits/latest",
     *     summary="جلب سجلات الخروج لآخر أسبوعين فقط",
     *     description="يقوم هذا المسار بإرجاع سجلات الخروج التي تمت خلال آخر 14 يوماً.",
     *     tags={"Student Exit Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب سجلات الخروج للأسبوعين الأخيرين بنجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/StudentExitLogResource")
     *         )
     *     )
     * )
     */
    public function latest()
    {
        $twoWeeksAgo = now()->subDays(14);
        $logs = StudentExitLog::with(['student.user', 'recorder'])
            ->where('exit_date', '>=', $twoWeeksAgo)
            ->orderBy('exit_date', 'desc')
            ->get();

        if ($logs->isEmpty()) {
            return $this->error('لا يوجد أي سجلات خروج خلال آخر أسبوعين', 404);
        }

        return $this->successResponse(
            StudentExitLogResource::collection($logs),
            'تم جلب سجلات الخروج للأسبوعين الأخيرين بنجاح'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/student-exits",
     *     summary="إنشاء خروج فردي",
     *     description="تسجيل خروج لطالب واحد. يمكن إرسال التاريخ والوقت يدوياً، أو تركهما فارغين ليتم استخدام الوقت الحالي تلقائياً.",
     *     tags={"Student Exit Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"student_id"},
     *             @OA\Property(property="student_id", type="integer", example=12, description="معرف الطالب (مطلوب)"),
     *             @OA\Property(property="exit_date", type="string", format="date", example="2025-01-15", description="تاريخ الخروج (اختياري، الافتراضي: تاريخ اليوم)"),
     *             @OA\Property(property="exit_time", type="string", format="time", example="13:45", description="وقت الخروج (اختياري، الافتراضي: الوقت الحالي)"),
     *             @OA\Property(property="return_time", type="string", format="time", nullable=true, example="14:20", description="وقت العودة المتوقع (اختياري)"),
     *             @OA\Property(property="exit_type", type="string", example="medical", description="نوع الخروج (مثلاً: طبي، مبكر، عادي)"),
     *             @OA\Property(property="reason", type="string", example="حالة صحية", description="سبب الخروج"),
     *             @OA\Property(property="note", type="string", example="خرج مع ولي الأمر", description="ملاحظات إضافية")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم تسجيل الخروج بنجاح",
     *         @OA\JsonContent(ref="#/components/schemas/StudentExitLogResource")
     *     )
     * )
     */
 public function store(StoreStudentExitLogRequest $request)
{
    $data = $request->validated();

    $data['exit_date'] = $data['exit_date'] ?? now()->toDateString();
    $data['exit_time'] = $data['exit_time'] ?? now()->format('H:i');
    $data['recorded_by'] = Auth::id();

    $log = $this->service->create($data);

    return $this->successResponse(
        new StudentExitLogResource($log),
        'تم تسجيل الخروج بنجاح',
        201
    );
}


    /**
     * @OA\Post(
     *     path="/api/student-exits/bulk",
     *     summary="إنشاء خروج جماعي",
     *     description="تسجيل خروج لمجموعة من الطلاب دفعة واحدة. مفيد لتسجيل خروج الشعب أو الرحلات. التاريخ والوقت اختياريان (الافتراضي: الآن للجميع).",
     *     tags={"Student Exit Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"students_ids"},
     *             @OA\Property(property="students_ids", type="array", description="مصفوفة بمعرفات الطلاب (مطلوب)",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             ),
     *             @OA\Property(property="exit_date", type="string", format="date", example="2025-01-15", description="تاريخ الخروج للجميع (اختياري، الافتراضي: اليوم)"),
     *             @OA\Property(property="exit_time", type="string", format="time", example="12:30", description="وقت الخروج للجميع (اختياري، الافتراضي: الآن)"),
     *             @OA\Property(property="return_time", type="string", format="time", nullable=true, description="وقت العودة (اختياري)"),
     *             @OA\Property(property="exit_type", type="string", nullable=true, example="school_trip", description="نوع الخروج الموحد"),
     *             @OA\Property(property="reason", type="string", nullable=true, example="رحلة علمية", description="السبب الموحد"),
     *             @OA\Property(property="note", type="string", nullable=true, description="ملاحظة موحدة")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم تسجيل الخروج الجماعي بنجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/StudentExitLogResource")
     *         )
     *     )
     * )
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'students_ids' => 'required|array|min:1',
            'students_ids.*' => 'exists:students,id',
            'exit_date' => 'nullable|date',
            'exit_time' => 'nullable|date_format:H:i',
            'return_time' => 'nullable|date_format:H:i',
            'exit_type' => 'nullable|string|max:50',
            'reason' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $data = $request->only([
            'exit_date',
            'exit_time',
            'return_time',
            'exit_type',
            'reason',
            'note'
        ]);

        /** @var \Illuminate\Contracts\Auth\Authenticatable|null $user */
        $user = Auth::user();
        $data['exit_date'] = $data['exit_date'] ?? now()->toDateString();
        $data['exit_time'] = $data['exit_time'] ?? now()->format('H:i');
        $data['recorded_by'] = Auth::id();


        $logs = $this->service->createBulk($request->students_ids, $data);

        return $this->successResponse(
            StudentExitLogResource::collection($logs),
            'تم تسجيل الخروج الجماعي بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/student-exits/{id}",
     *     summary="عرض سجل خروج واحد",
     *     tags={"Student Exit Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="رقم السجل",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(ref="#/components/schemas/StudentExitLogResource")
     *     ),
     *     @OA\Response(response=404, description="السجل غير موجود")
     * )
     */
    public function show($id)
    {
        $log = StudentExitLog::with(['student.user', 'recorder'])->find($id);

        if (!$log) {
            return $this->error('السجل غير موجود', 404);
        }

        return $this->successResponse(
            new StudentExitLogResource($log),
            'تفاصيل سجل الخروج'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/student-exits/{id}",
     *     summary="تعديل سجل خروج",
     *     tags={"Student Exit Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="رقم السجل",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="exit_date", type="string"),
     *             @OA\Property(property="exit_time", type="string"),
     *             @OA\Property(property="return_time", type="string"),
     *             @OA\Property(property="exit_type", type="string"),
     *             @OA\Property(property="reason", type="string"),
     *             @OA\Property(property="note", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="تم التعديل بنجاح"),
     *     @OA\Response(response=404, description="غير موجود")
     * )
     */
    public function update(UpdateStudentExitLogRequest $request, $id)
    {
        $log = StudentExitLog::find($id);

        if (!$log) {
            return $this->error('السجل غير موجود', 404);
        }

        $updated = $this->service->update($log, $request->validated());

        return $this->successResponse(
            new StudentExitLogResource($updated),
            'تم تحديث السجل بنجاح'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/student-exits/{id}",
     *     summary="حذف سجل خروج",
     *     tags={"Student Exit Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="تم الحذف بنجاح"),
     *     @OA\Response(response=404, description="السجل غير موجود")
     * )
     */
    public function destroy($id)
    {
        $log = StudentExitLog::find($id);

        if (!$log) {
            return $this->error('السجل غير موجود', 404);
        }

        $this->service->delete($log);

        return $this->successResponse(null, 'تم حذف السجل بنجاح');
    }
}
