<?php

namespace Modules\DoorSessions\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Attendances\Models\Attendance;
use Modules\DoorSessions\Models\DoorSession;
use Modules\DoorSessions\Services\GenerateDoorSessionService;
use Modules\DoorSessions\Http\Requests\UseDoorSessionRequest;
use Modules\Shared\Traits\SuccessResponseTrait;

class UseDoorSessionController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/door-sessions/use",
     *     summary="تسجيل حضور الطالب عبر جهاز الباب (رمز QR)",
     *     description="يُستخدم هذا المسار عندما يقوم الطالب بمسح رمز QR على جهاز الباب. يتحقق من صلاحية الجلسة، يسجّل الحضور، ويولّد جلسة جديدة للجهاز.",
     *     tags={"Door Sessions"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="البيانات المطلوبة لاستخدام الجلسة",
     *         @OA\JsonContent(
     *             required={"session_token"},
     *             @OA\Property(
     *                 property="session_token",
     *                 type="string",
     *                 example="9e8b5d4c1f2a3b6d7c8e9f0a1b2c3d4e",
     *                 description="رمز الجلسة (token) المولد من جهاز الباب"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تسجيل حضور الطالب بنجاح عبر الجهاز",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تسجيل دخولك عبر الباب بنجاح."),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="رمز الجلسة غير صالح أو لا يوجد طالب مرتبط بهذا المستخدم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="رمز الجلسة غير صالح أو منتهي أو مستخدم مسبقًا."),
     *             @OA\Property(property="data", type="null", nullable=true)
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
public function __invoke(UseDoorSessionRequest $request, GenerateDoorSessionService $generator)
{
    $user = Auth::user();

    // 1️⃣ التحقق من وجود جلسة صالحة
    $session = DoorSession::where('session_token', $request->session_token)
        ->where('is_used', false)
        ->where('expires_at', '>', Carbon::now())
        ->first();

    if (! $session) {
        return $this->error('رمز الجلسة غير صالح أو منتهي أو مستخدم مسبقًا.', 422);
    }

    // 2️⃣ التحقق من وجود طالب مرتبط بالمستخدم
    $student = $user->student;

    if (! $student) {
        return $this->error('لم يتم العثور على طالب مرتبط بهذا الحساب.', 422);
    }

    // 3️⃣ التحقق من وجود دفعة مرتبطة بالطالب
    $latestBatchStudent = $student->latestBatchStudent;
    $batchId = $latestBatchStudent?->batch_id;

    if (! $batchId) {
        return $this->error('لم يتم العثور على دفعة مرتبطة بالطالب ' . $student->name .$student->first_name . ' ' . $student->last_name . '.', 404);
    }

    // 4️⃣ التحقق من عدم تسجيل الحضور مسبقًا في نفس اليوم
    $alreadyAttended = Attendance::whereDate('attendance_date', Carbon::today())
        ->where('student_id', $student->id)
        ->exists();

    if ($alreadyAttended) {
        return $this->error('تم تسجيل حضورك مسبقًا اليوم يا ' .$student->first_name . ' ' . $student->last_name . '.', 409);
    }

    // 5️⃣ تحديث حالة الجلسة كـ مستخدمة
    $session->update([
        'is_used'    => true,
        'student_id' => $student->id,
        'used_at'    => now(),
    ]);

    // 6️⃣ تسجيل الحضور
    Attendance::create([
        'institute_branch_id' => $student->institute_branch_id,
        'student_id'          => $student->id,
        'batch_id'            => $batchId,
        'attendance_date'     => Carbon::today(),
        'status'              => 'present',
        'recorded_by'         => null,
        'device_id'           => $session->device_id,
        'recorded_at'         => now(),
    ]);

    // 7️⃣ توليد جلسة جديدة (QR جديد)
    $generator->createForDevice($session->device);

    return $this->successResponse(
        null,
        'تم تسجيل حضور الطالب ' . $student->first_name . ' ' . $student->last_name . ' بنجاح عبر الجهاز.',
        200
    );
}

}
