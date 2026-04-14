<?php

namespace Modules\ClassSchedules\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ClassSchedules\Models\ClassSchedule;
use Modules\ClassSchedules\Http\Requests\StoreClassScheduleRequest;
use Modules\ClassSchedules\Http\Requests\UpdateClassScheduleRequest;
use Modules\ClassSchedules\Http\Resources\ClassScheduleResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Illuminate\Http\Request;
use Modules\Exams\Models\Exam;
use Modules\ClassSchedules\Services\ClassScheduleTypeService;
use Carbon\Carbon;

class ClassSchedulesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/class-schedules",
     *     summary="قائمة جميع جداول الدروس",
     *     tags={"ClassSchedules"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع جداول الدروس بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع جداول الدروس بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                     @OA\Property(property="day_of_week", type="string", example="monday", nullable=true),
     *                     @OA\Property(property="schedule_date", type="string", format="date", example="2025-09-29", nullable=true),
     *                     @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                     @OA\Property(property="end_time", type="string", format="time", example="10:30:00"),
     *                     @OA\Property(property="room_number", type="string", example="A101", nullable=true),
     *                     @OA\Property(property="is_default", type="boolean", example=false),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="description", type="string", example="جدول رمضان", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:09:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:09:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد جداول دروس",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي جداول دروس مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $classSchedules = ClassSchedule::with([
            'batchSubject.batch',
            'batchSubject.subject',
            'batchSubject.instructorSubject.instructor',
            'classRoom'
        ])->get();

        if ($classSchedules->isEmpty()) {
            return $this->error('لا يوجد أي جداول دروس مسجلة حالياً', 404);
        }

        return $this->successResponse(
            ClassScheduleResource::collection($classSchedules),
            'تم جلب جميع جداول الدروس بنجاح'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/class-schedules",
     *     summary="إضافة جدول درس جديد",
     *     tags={"ClassSchedules"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"batch_subject_id","start_time","end_time"},
     *             @OA\Property(property="batch_subject_id", type="integer", example=1),
     *             @OA\Property(property="day_of_week", type="string", example="monday", nullable=true),
     *             @OA\Property(property="schedule_date", type="string", format="date", example="2025-09-29", nullable=true),
     *             @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="10:30:00"),
     *             @OA\Property(property="room_number", type="string", example="A101", nullable=true),
     *             @OA\Property(property="is_default", type="boolean", example=false),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="description", type="string", example="جدول رمضان", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء جدول الدرس بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء جدول الدرس بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                 @OA\Property(property="day_of_week", type="string", example="monday", nullable=true),
     *                 @OA\Property(property="schedule_date", type="string", format="date", example="2025-09-29", nullable=true),
     *                 @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="10:30:00"),
     *                 @OA\Property(property="room_number", type="string", example="A101", nullable=true),
     *                 @OA\Property(property="is_default", type="boolean", example=false),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="description", type="string", example="جدول رمضان", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:09:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:09:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreClassScheduleRequest $request)
    {
        $classSchedule = ClassSchedule::create($request->validated());

        return $this->successResponse(
            new ClassScheduleResource($classSchedule->load(['batchSubject', 'classRoom'])),
            'تم إنشاء جدول الدرس بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/class-schedules/{id}",
     *     summary="عرض تفاصيل جدول درس محدد",
     *     tags={"ClassSchedules"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جدول الدرس",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات جدول الدرس بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات جدول الدرس بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                 @OA\Property(property="day_of_week", type="string", example="monday", nullable=true),
     *                 @OA\Property(property="schedule_date", type="string", format="date", example="2025-09-29", nullable=true),
     *                 @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="10:30:00"),
     *                 @OA\Property(property="room_number", type="string", example="A101", nullable=true),
     *                 @OA\Property(property="is_default", type="boolean", example=false),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="description", type="string", example="جدول رمضان", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:09:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:09:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جدول الدرس غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جدول الدرس غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $classSchedule = ClassSchedule::with(['batchSubject', 'classRoom'])->find($id);

        if (!$classSchedule) {
            return $this->error('جدول الدرس غير موجود', 404);
        }

        return $this->successResponse(
            new ClassScheduleResource($classSchedule),
            'تم جلب بيانات جدول الدرس بنجاح'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/class-schedules/{id}",
     *     summary="تحديث جدول درس",
     *     tags={"ClassSchedules"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جدول الدرس",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="batch_subject_id", type="integer", example=2),
     *             @OA\Property(property="day_of_week", type="string", example="tuesday", nullable=true),
     *             @OA\Property(property="schedule_date", type="string", format="date", example="2025-09-30", nullable=true),
     *             @OA\Property(property="start_time", type="string", format="time", example="10:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="11:30:00"),
     *             @OA\Property(property="room_number", type="string", example="B202", nullable=true),
     *             @OA\Property(property="is_default", type="boolean", example=true),
     *             @OA\Property(property="is_active", type="boolean", example=false),
     *             @OA\Property(property="description", type="string", example="امتحانات نصف الفصل", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث جدول الدرس بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث جدول الدرس بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=2),
     *                 @OA\Property(property="day_of_week", type="string", example="tuesday", nullable=true),
     *                 @OA\Property(property="schedule_date", type="string", format="date", example="2025-09-30", nullable=true),
     *                 @OA\Property(property="start_time", type="string", format="time", example="10:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="11:30:00"),
     *                 @OA\Property(property="room_number", type="string", example="B202", nullable=true),
     *                 @OA\Property(property="is_default", type="boolean", example=true),
     *                 @OA\Property(property="is_active", type="boolean", example=false),
     *                 @OA\Property(property="description", type="string", example="امتحانات نصف الفصل", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:09:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:09:30Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جدول الدرس غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جدول الدرس غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateClassScheduleRequest $request, $id)
    {
        $classSchedule = ClassSchedule::find($id);

        if (!$classSchedule) {
            return $this->error('جدول الدرس غير مو  جود', 404);
        }

        $classSchedule->update($request->validated());

        return $this->successResponse(
            new ClassScheduleResource($classSchedule->load(['batchSubject', 'classRoom'])),
            'تم تحديث جدول الدرس بنجاح'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/class-schedules/{id}",
     *     summary="حذف جدول درس",
     *     tags={"ClassSchedules"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جدول الدرس",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف جدول الدرس بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف جدول الدرس بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جدول الدرس غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جدول الدرس غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $classSchedule = ClassSchedule::find($id);

        if (!$classSchedule) {
            return $this->error('جدول الدرس غير موجود', 404);
        }

        $classSchedule->delete();

        return $this->successResponse(
            null,
            'تم حذف جدول الدرس بنجاح'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/class-schedules/today",
     *     summary="جلب برنامج دوام اليوم (مع فلاتر اختيارية)",
     *     description="
يعيد هذا المسار برنامج دوام اليوم الحالي.

🧠 **السلوك:**
- في حال وجود برنامج مطابق للفلاتر → يتم إرجاع الحصص.
- في حال عدم وجود أي برنامج → تعاد مصفوفات فارغة مع periods_count = 0.

⚠️ في جميع الحالات:
- Status Code = 200
- لا يتم اعتبار عدم وجود برنامج خطأ.

📌 يعتمد هذا المسار على العلاقات التالية:
- class_schedules
- batch_subjects
- batches (لتحديد الموقع الجغرافي)
- batch_employees → employees (المشرف الإداري)
",
     *     tags={"Class Schedules"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="is_default",
     *         in="query",
     *         required=false,
     *         description="
فلترة البرنامج الافتراضي:
- true → البرنامج الافتراضي فقط
- false → غير الافتراضي فقط
- غير مرسل → بدون فلترة
",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *
     *     @OA\Parameter(
     *         name="institute_branch_id",
     *         in="query",
     *         required=false,
     *         description="
فلترة حسب الموقع الجغرافي (فرع المعهد).

⚠️ ملاحظة:
- الموقع الجغرافي لا يُخزن مباشرة في جدول class_schedules.
- يتم الاستنتاج عبر:
  class_schedules → batch_subjects → batches → institute_branch_id
",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="
نجاح الطلب.

📌 حالتان محتملتان داخل نفس الاستجابة:
1️⃣ يوجد برنامج دوام → periods تحتوي بيانات
2️⃣ لا يوجد برنامج → periods فارغة و periods_count = 0
",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب برنامج دوام اليوم بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="filters",
     *                     type="object",
     *                     description="الفلاتر التي تم تطبيقها فعليًا على الاستعلام",
     *                     @OA\Property(property="is_default", type="string", nullable=true, example="true"),
     *                     @OA\Property(property="institute_branch_id", type="string", nullable=true, example="1")
     *                 ),
     *
     *                 @OA\Property(
     *                     property="periods_count",
     *                     type="integer",
     *                     example=5,
     *                     description="عدد الحصص الفعلية لليوم بدون تكرار رقم الحصة"
     *                 ),
     *
     *                 @OA\Property(
     *                     property="periods",
     *                     type="object",
     *                     description="الحصص مجمعة حسب رقم الحصة (مفاتيح ديناميكية مثل: الحصة 1، الحصة 2 …)",
     *                     additionalProperties={
     *                         @OA\Schema(
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="batch_name", type="string", example="بكالوريا علمي شباب شتاء 2024"),
     *                                 @OA\Property(
     *                                     property="supervisor",
     *                                     type="object",
     *                                     nullable=true,
     *                                     @OA\Property(property="name", type="string", example="ريم صالح"),
     *                                     @OA\Property(
     *                                         property="photo",
     *                                         type="string",
     *                                         nullable=true,
     *                                         example="http://127.0.0.1:8000/storage/employees/photos/example.jpg"
     *                                     )
     *                                 ),
     *                                 @OA\Property(property="subject", type="string", example="اللغة العربية"),
     *                                 @OA\Property(property="class_room", type="string", example="القاعة 7"),
     *                                 @OA\Property(property="start_time", type="string", example="08:00:00"),
     *                                 @OA\Property(property="end_time", type="string", example="09:00:00"),
     *                                 @OA\Property(property="description", type="string", nullable=true, example="البرنامج الافتراضي"),
     *                                 @OA\Property(property="is_default", type="boolean", example=true)
     *                             )
     *                         )
     *                     }
     *                 )
     *             ),
     *
     *             @OA\Examples(
     *                 example="with_data",
     *                 summary="برنامج دوام موجود",
     *                 value={
     *                     "status"=true,
     *                     "message"="تم جلب برنامج دوام اليوم بنجاح",
     *                     "data"={
     *                         "filters"={"is_default"="true","institute_branch_id"="1"},
     *                         "periods_count"=2,
     *                         "periods"={
     *                             "الحصة 1"={{
     *                                 "batch_name"="بكالوريا علمي شباب شتاء 2024",
     *                                 "supervisor"={
     *                                     "name"="ريم صالح",
     *                                     "photo"="http://127.0.0.1:8000/storage/employees/photos/example.jpg"
     *                                 },
     *                                 "subject"="اللغة العربية",
     *                                 "class_room"="القاعة 7",
     *                                 "start_time"="08:00:00",
     *                                 "end_time"="09:00:00",
     *                                 "description"="البرنامج الافتراضي",
     *                                 "is_default"=true
     *                             }}
     *                         }
     *                     }
     *                 }
     *             ),
     *
     *             @OA\Examples(
     *                 example="empty_result",
     *                 summary="لا يوجد برنامج دوام",
     *                 value={
     *                     "status"=true,
     *                     "message"="لا يوجد برنامج دوام مطابق للفلاتر المحددة",
     *                     "data"={
     *                         "filters"={"is_default"="true","institute_branch_id"="3"},
     *                         "periods_count"=0,
     *                         "periods"={}
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح (المستخدم غير مسجّل الدخول)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ غير متوقع في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء جلب برنامج الدوام")
     *         )
     *     )
     * )
     */


    public function getTodaySchedule(Request $request)
    {
        // 🔹 اسم اليوم الحالي
        $today = now()->format('l');

        // 🔹 قراءة الفلاتر (اختيارية)
        $isDefault = $request->query('is_default'); // null | 0 | 1
        $instituteBranchId = $request->query('institute_branch_id'); // null | int

        // 🔹 الاستعلام الأساسي
        $query = ClassSchedule::query()
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->with([
                'batchSubject.subject',
                'batchSubject.batch.batchEmployees.employee:id,first_name,last_name,photo_path',
                'batchSubject.batch:id,name,institute_branch_id',
                'classRoom',
            ]);

        // 🔹 فلترة البرنامج الافتراضي (عند الإرسال فقط)
        $query->when(!is_null($isDefault), function ($q) use ($isDefault) {
            $q->where('is_default', filter_var($isDefault, FILTER_VALIDATE_BOOLEAN));
        });

        // 🔹 فلترة حسب الموقع الجغرافي (عبر الشعبة)
        $query->when($instituteBranchId, function ($q) use ($instituteBranchId) {
            $q->whereHas('batchSubject.batch', function ($batchQuery) use ($instituteBranchId) {
                $batchQuery->where('institute_branch_id', $instituteBranchId);
            });
        });

        // 🔹 التنفيذ
        $schedules = $query
            ->orderBy('period_number')
            ->get();

        // 🔹 في حال لا يوجد بيانات
        if ($schedules->isEmpty()) {
            return $this->successResponse(
                [
                    'filters' => [
                        'is_default'          => $isDefault,
                        'institute_branch_id' => $instituteBranchId,
                    ],
                    'periods_count' => 0,
                    'periods'       => (object) [],
                ],
                'لا يوجد برنامج دوام مطابق للفلاتر المحددة'
            );
        }

        // 🔹 جلب امتحانات اليوم مرة واحدة
        $todayDate = now()->toDateString();

        $todayExams = Exam::with('batchSubject')
            ->whereDate('exam_date', $todayDate)
            ->get();

        // 🔹 تهيئة السيرفس
        $scheduleTypeService = new ClassScheduleTypeService($todayExams);




        // 🔹 تنسيق النتيجة
        $periods = [];

        foreach ($schedules as $schedule) {
            $periodKey = 'الحصة ' . $schedule->period_number;

            $batch = $schedule->batchSubject->batch ?? null;
            $supervisor = optional(
                $batch?->batchEmployees->first()
            )->employee;
            $type = $scheduleTypeService->resolve($schedule);
            $periods[$periodKey][] = [
                'batch_name' => $batch?->name,

                'supervisor' => $supervisor ? [
                    'name'  => $supervisor->first_name . ' ' . $supervisor->last_name,
                    'photo' => $supervisor->photo_url,
                ] : null,

                'subject'     => $schedule->batchSubject->subject->name ?? null,
                'class_room'  => $schedule->classRoom->name ?? null,
                'type'        => $type, // lesson | exam
                'start_time'  => $schedule->start_time,
                'end_time'    => $schedule->end_time,
                'description' => $schedule->description,
                'is_default'  => $schedule->is_default,
            ];
        }

        // 🔹 عدد الحصص (بدون تكرار)
        $periodsCount = $schedules
            ->pluck('period_number')
            ->unique()
            ->count();

        return $this->successResponse(
            [
                'filters' => [
                    'is_default'           => $isDefault,
                    'institute_branch_id'  => $instituteBranchId,
                ],
                'periods_count' => $periodsCount,
                'periods'       => $periods,
            ],
            'تم جلب برنامج دوام اليوم بنجاح'
        );
    }
}
