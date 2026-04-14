<?php

namespace Modules\Exams\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Exams\Filters\ExamFilter;
use Modules\Exams\Models\Exam;
use Modules\Exams\Http\Requests\StoreExamRequest;
use Modules\Exams\Http\Requests\UpdateExamRequest;
use Modules\Exams\Http\Resources\ExamResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class ExamsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/exams",
     *     summary="قائمة جميع الامتحانات",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع الامتحانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع الامتحانات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="امتحان منتصف الفصل"),
     *                     @OA\Property(property="exam_date", type="string", format="date", example="2023-01-15"),
     *                     @OA\Property(property="exam_time", type="string", format="time", example="10:00:00", description="وقت بدء الامتحان (ساعة:دقيقة:ثانية)"),
     *                     @OA\Property(property="exam_end_time", type="string", format="time", example="12:00:00", description="وقت نهاية الامتحان (ساعة:دقيقة:ثانية)"),
     *                     @OA\Property(property="total_marks", type="integer", example=100),
     *                     @OA\Property(property="passing_marks", type="integer", example=60),
     *                     @OA\Property(property="status", type="string", example="scheduled"),
     *                     @OA\Property(property="exam_type", type="string", example="midterm"),
     *                     @OA\Property(property="remarks", type="string", example="امتحان يشمل الوحدتين الأولى والثانية"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد امتحانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي امتحان مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $exams = Exam::all();

        if ($exams->isEmpty()) {
            return $this->error('لا يوجد أي امتحان مسجل حالياً', 404);
        }

        return $this->successResponse(
            ExamResource::collection($exams),
            'تم جلب جميع الامتحانات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/exams",
     *     summary="إضافة امتحان جديد",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"batch_subject_id","name","exam_date","total_marks","passing_marks","status","exam_type_id"},
     *             @OA\Property(property="batch_subject_id", type="integer", example=1, description="معرف المادة في الدورة"),
     *             @OA\Property(property="name", type="string", example="امتحان منتصف الفصل"),
     *             @OA\Property(property="exam_date", type="string", format="date", example="2023-01-15", description="تاريخ الامتحان (YYYY-MM-DD)"),
     *             @OA\Property(property="exam_time", type="string", format="time", example="10:00:00", description="وقت بدء الامتحان (HH:MM:SS)"),
     *             @OA\Property(property="exam_end_time", type="string", format="time", example="12:00:00", description="وقت نهاية الامتحان (HH:MM:SS) - اختياري"),
     *             @OA\Property(property="total_marks", type="integer", example=100),
     *             @OA\Property(property="passing_marks", type="integer", example=60),
     *             @OA\Property(property="status", type="string", example="scheduled"),
     *             @OA\Property(property="exam_type_id", type="integer", example=2, description="معرف نوع الامتحان (من جدول exam_types)"),
     *             @OA\Property(property="remarks", type="string", example="امتحان يشمل الوحدتين الأولى والثانية", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="امتحان منتصف الفصل"),
     *                 @OA\Property(property="exam_date", type="string", format="date", example="2023-01-15"),
     *                 @OA\Property(property="exam_time", type="string", format="time", example="10:00:00", description="وقت بدء الامتحان"),
     *                 @OA\Property(property="exam_end_time", type="string", format="time", example="12:00:00", description="وقت نهاية الامتحان"),
     *                 @OA\Property(property="total_marks", type="integer", example=100),
     *                 @OA\Property(property="passing_marks", type="integer", example=60),
     *                 @OA\Property(property="status", type="string", example="scheduled"),
     *                 @OA\Property(property="exam_type_id", type="integer", example=2),
     *                 @OA\Property(property="remarks", type="string", example="امتحان يشمل الوحدتين الأولى والثانية"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من الصحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreExamRequest $request)
    {
        $exam = Exam::create($request->validated());

        return $this->successResponse(
            new ExamResource($exam),
            'تم إنشاء الامتحان بنجاح',
            201
        );
    }
    /**
     * @OA\Get(
     *     path="/api/exams/{id}",
     *     summary="عرض تفاصيل امتحان محدد",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الامتحان",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="امتحان منتصف الفصل"),
     *                 @OA\Property(property="exam_date", type="string", format="date", example="2023-01-15"),
     *                 @OA\Property(property="exam_time", type="string", format="time", example="10:00:00", description="وقت بدء الامتحان (HH:MM:SS)"),
     *                 @OA\Property(property="exam_end_time", type="string", format="time", example="12:00:00", description="وقت نهاية الامتحان (HH:MM:SS)"),
     *                 @OA\Property(property="total_marks", type="integer", example=100),
     *                 @OA\Property(property="passing_marks", type="integer", example=60),
     *                 @OA\Property(property="status", type="string", example="scheduled"),
     *                 @OA\Property(property="exam_type_id", type="integer", example=2, description="معرف نوع الامتحان"),
     *                 @OA\Property(property="remarks", type="string", example="امتحان يشمل الوحدتين الأولى والثانية", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return $this->error('الامتحان غير موجود', 404);
        }

        return $this->successResponse(
            new ExamResource($exam),
            'تم جلب بيانات الامتحان بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/exams/{id}",
     *     summary="تحديث بيانات امتحان",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الامتحان",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="البيانات التي تريد تحديثها (جميع الحقول اختيارية)",
     *         @OA\JsonContent(
     *             @OA\Property(property="batch_subject_id", type="integer", example=2, description="معرف المادة في الدورة"),
     *             @OA\Property(property="name", type="string", example="امتحان نهائي"),
     *             @OA\Property(property="exam_date", type="string", format="date", example="2023-02-15", description="تاريخ الامتحان (YYYY-MM-DD)"),
     *             @OA\Property(property="exam_time", type="string", format="time", example="09:30:00", description="وقت بدء الامتحان (HH:MM:SS)"),
     *             @OA\Property(property="exam_end_time", type="string", format="time", example="11:30:00", description="وقت نهاية الامتحان (HH:MM:SS) - اختياري"),
     *             @OA\Property(property="total_marks", type="integer", example=100),
     *             @OA\Property(property="passing_marks", type="integer", example=60),
     *             @OA\Property(property="status", type="string", example="completed"),
     *             @OA\Property(property="exam_type_id", type="integer", example=3, description="معرف نوع الامتحان (من جدول exam_types)"),
     *             @OA\Property(property="remarks", type="string", example="امتحان شامل لكامل المنهج", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="امتحان نهائي"),
     *                 @OA\Property(property="exam_date", type="string", format="date", example="2023-02-15"),
     *                 @OA\Property(property="exam_time", type="string", format="time", example="09:30:00", description="وقت بدء الامتحان"),
     *                 @OA\Property(property="exam_end_time", type="string", format="time", example="11:30:00", description="وقت نهاية الامتحان"),
     *                 @OA\Property(property="total_marks", type="integer", example=100),
     *                 @OA\Property(property="passing_marks", type="integer", example=60),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="exam_type_id", type="integer", example=3),
     *                 @OA\Property(property="remarks", type="string", example="امتحان شامل لكامل المنهج"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من الصحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateExamRequest $request, $id)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return $this->error('الامتحان غير موجود', 404);
        }

        $exam->update($request->validated());

        return $this->successResponse(
            new ExamResource($exam),
            'تم تحديث بيانات الامتحان بنجاح',
            200
        );
    }
    /**
     * @OA\Delete(
     *     path="/api/exams/{id}",
     *     summary="حذف امتحان",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الامتحان",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الامتحان بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $exam = Exam::find($id);

            if (!$exam) {
                return $this->error('الامتحان غير موجود', 404);
            }

        $exam->delete();

        return $this->successResponse(
            null,
            'تم حذف الامتحان بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/exams/{date}",
     *     summary="جلب الامتحانات حسب تاريخ محدد مع فلاتر اختيارية",
     *     description="
     *     تُستخدم هذه الواجهة لجلب جميع الامتحانات في تاريخ معين، 
     *     مع إمكانية تطبيق فلاتر اختيارية مثل:
     *     - فرع المعهد (الموقع الجغرافي)
     *     - الشعبة
     *     - نوع الشعبة (ذكور / إناث / مختلطة)
     *     
     *     جميع الفلاتر اختيارية ولا تؤثر على النتيجة في حال عدم إرسالها.
     *     ",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         required=true,
     *         description="تاريخ الامتحان بالصيغة Y-m-d",
     *         @OA\Schema(type="string", format="date", example="2026-01-10")
     *     ),
     *
     *     @OA\Parameter(
     *         name="branch_id",
     *         in="query",
     *         required=false,
     *         description="فلترة حسب فرع المعهد (الموقع الجغرافي)",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="query",
     *         required=false,
     *         description="فلترة حسب الشعبة (Batch)",
     *         @OA\Schema(type="integer", example=12)
     *     ),
     *
     *     @OA\Parameter(
     *         name="gender",
     *         in="query",
     *         required=false,
     *         description="فلترة حسب نوع الشعبة",
     *         @OA\Schema(
     *             type="string",
     *             enum={"male","female","mixed"},
     *             example="female"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الامتحانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الامتحانات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="امتحان نصف الفصل"),
     *                     @OA\Property(property="exam_date", type="string", format="date", example="2026-01-10"),
     *                     @OA\Property(property="exam_time", type="string", example="10:30", description="وقت بدء الامتحان"),
     *                     @OA\Property(property="total_marks", type="integer", example=100),
     *                     @OA\Property(property="passing_marks", type="integer", example=60),
     *                     @OA\Property(property="status", type="string", example="scheduled"),
     *                     @OA\Property(property="exam_type_id", type="integer", example=1),
     *                     @OA\Property(property="remarks", type="string", nullable=true, example="ملاحظات إضافية"),
     *                     @OA\Property(
     *                         property="batch_subject",
     *                         type="object",
     *                         @OA\Property(
     *                             property="batch",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=12),
     *                             @OA\Property(property="name", type="string", example="الشعبة الصباحية"),
     *                             @OA\Property(property="gender_type", type="string", example="female"),
     *                             @OA\Property(property="institute_branch_id", type="integer", example=3)
     *                         ),
     *                         @OA\Property(
     *                             property="class_room",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=5),
     *                             @OA\Property(property="code", type="string", example="CR-101")
     *                         ),
     *                         @OA\Property(
     *                             property="subject",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=7),
     *                             @OA\Property(property="name", type="string", example="الرياضيات")
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-01T09:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-01-01T09:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="لا توجد امتحانات مطابقة للفلاتر المحددة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا توجد امتحانات مطابقة للفلاتر المحددة"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من الفلاتر",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "gender": {"قيمة الجنس غير صحيحة"}
     *                 }
     *             )
     *         )
     *     )
     * )
     */


    public function getExamsByDate(Request $request, string $date)
    {
        if (!\Carbon\Carbon::hasFormat($date, 'Y-m-d')) {
            return $this->error('التاريخ غير صالح', 400);
        }

        $filter = ExamFilter::fromRequest($request);

        $exams = Exam::query()
            ->byDate($date)
            ->filter($filter)
            ->with([
                'batchSubject:id,batch_id,class_room_id,instructor_subject_id',
                'batchSubject.batch:id,name,gender_type,institute_branch_id',
                'batchSubject.classRoom:id,code',
                'batchSubject.instructorSubject.subject:id,name',
            ])
            ->orderBy('exam_time')
            ->get();

        return $this->successResponse(
            ExamResource::collection($exams),
            'تم جلب الامتحانات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/exams/{id}/postpone",
     *     summary="تأجيل امتحان محدد",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الامتحان المراد تأجيله",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="exam_date", type="string", format="date", example="2026-03-01", description="تاريخ الامتحان الجديد"),
     *             @OA\Property(property="exam_time", type="string", format="time", example="10:00", description="وقت بدء الامتحان الجديد (HH:MM)"),
     *             @OA\Property(property="exam_end_time", type="string", format="time", example="12:00", description="وقت نهاية الامتحان الجديد (HH:MM)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تأجيل الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تأجيل الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="امتحان منتصف الفصل"),
     *                 @OA\Property(property="exam_date", type="string", format="date", example="2026-03-01"),
     *                 @OA\Property(property="exam_time", type="string", format="time", example="10:00:00"),
     *                 @OA\Property(property="exam_end_time", type="string", format="time", example="12:00:00"),
     *                 @OA\Property(property="total_marks", type="integer", example=100),
     *                 @OA\Property(property="passing_marks", type="integer", example=60),
     *                 @OA\Property(property="status", type="string", example="postponed"),
     *                 @OA\Property(property="exam_type_id", type="integer", example=2),
     *                 @OA\Property(property="remarks", type="string", example="امتحان يشمل الوحدتين الأولى والثانية", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-15T09:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="لا يمكن تأجيل امتحان مضى الوقت عليه أو بيانات غير صحيحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يمكن تأجيل امتحان مضى الوقت عليه"),
     *             @OA\Property(property="data", type="null")
     *         )
     *         )
     *     )
     * )
     */
    public function postpone(Request $request, $id)
    {
        $request->validate([
            'exam_date'     => 'required|date',
            'exam_time'     => 'required|date_format:H:i',
            'exam_end_time' => 'required|date_format:H:i|after:exam_time',
        ]);

        $exam = Exam::find($id);

        if (!$exam) {
            return $this->error('الامتحان غير موجود', 404);
        }

        if (!$exam->canBeModified()) {
            return $this->error('لا يمكن تأجيل امتحان مضى الوقت عليه', 422);
        }

        $exam->update([
            'exam_date'     => $request->exam_date,
            'exam_time'     => $request->exam_time,
            'exam_end_time' => $request->exam_end_time,
            'status'        => 'postponed',
        ]);

        return $this->successResponse(
            $exam,
            'تم تأجيل الامتحان بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/exams/{id}/cancel",
     *     summary="إلغاء امتحان محدد",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الامتحان المراد إلغاؤه",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم إلغاء الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إلغاء الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="امتحان منتصف الفصل"),
     *                 @OA\Property(property="exam_date", type="string", format="date", example="2026-03-01"),
     *                 @OA\Property(property="exam_time", type="string", format="time", example="10:00:00"),
     *                 @OA\Property(property="exam_end_time", type="string", format="time", example="12:00:00"),
     *                 @OA\Property(property="total_marks", type="integer", example=100),
     *                 @OA\Property(property="passing_marks", type="integer", example=60),
     *                 @OA\Property(property="status", type="string", example="cancelled"),
     *                 @OA\Property(property="exam_type_id", type="integer", example=2),
     *                 @OA\Property(property="remarks", type="string", example="امتحان يشمل الوحدتين الأولى والثانية", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-15T09:30:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="لا يمكن إلغاء امتحان مضى الوقت عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يمكن إلغاء امتحان مضى الوقت عليه"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function cancel($id)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return $this->error('الامتحان غير موجود', 404);
        }

        if (!$exam->canBeModified()) {
            return $this->error('لا يمكن إلغاء امتحان مضى الوقت عليه', 422);
        }

        $exam->update([
            'status' => 'cancelled',
        ]);

        return $this->successResponse(
            $exam,
            'تم إلغاء الامتحان بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/exams/{id}/complete",
     *     summary="وضع امتحان محدد كمكتمل",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الامتحان المراد وضعه كمكتمل",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم وضع الامتحان كمكتمل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم وضع الامتحان كمكتمل بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="امتحان منتصف الفصل"),
     *                 @OA\Property(property="exam_date", type="string", format="date", example="2026-03-01"),
     *                 @OA\Property(property="exam_time", type="string", format="time", example="10:00:00"),
     *                 @OA\Property(property="exam_end_time", type="string", format="time", example="12:00:00"),
     *                 @OA\Property(property="total_marks", type="integer", example=100),
     *                 @OA\Property(property="passing_marks", type="integer", example=60),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="exam_type_id", type="integer", example=2),
     *                 @OA\Property(property="remarks", type="string", example="امتحان يشمل الوحدتين الأولى والثانية", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-15T10:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="لا يمكن وضع هذا الامتحان كمكتمل لأنه مضى الوقت عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يمكن وضع هذا الامتحان كمكتمل لأنه مضى الوقت عليه"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function complete($id)
    {
        $exam = Exam::findOrFail($id);

        // مثال: لا يمكن وضع completed إذا تاريخ الامتحان قبل اليوم
        if (!$exam->exam_date->lt(now()->startOfDay())) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن وضع هذا الامتحان كمكتمل لأنه مضى الوقت عليه'
            ], 422);
        }   

        $exam->update([
            'status' => 'completed'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم وضع الامتحان كمكتمل بنجاح',
            'data' => $exam
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/exams/filtered",
     *     summary="جلب كل الامتحانات مع إمكانية الفلترة حسب الشعبة والطالب",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="query",
     *         required=false,
     *         description="معرف الشعبة (اختياري)",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         required=false,
     *         description="معرف الطالب (اختياري)",
     *         @OA\Schema(type="integer", example=12)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الامتحانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="تم جلب الامتحانات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="امتحان منتصف الفصل"),
     *                     @OA\Property(property="exam_date", type="string", format="date", example="2026-03-01"),
     *                     @OA\Property(property="exam_time", type="string", format="time", example="10:00:00"),
     *                     @OA\Property(property="exam_type", type="string", example="Midterm"),
     *                     @OA\Property(property="total_marks", type="integer", example=100),
     *                     @OA\Property(property="passing_marks", type="integer", example=60),
     *                     @OA\Property(property="status", type="string", example="scheduled")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="بيانات غير صالحة أو بارامتر غير صحيح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="بيانات غير صالحة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function getFilteredExams(Request $request)
    {
        $batchId = $request->query('batch_id');
        $studentId = $request->query('student_id');

        // البداية: جلب الامتحانات مع الفلترة الصحيحة على مستوى الاستعلام الأساسي
        $examsQuery = Exam::query()
            ->with(['examType', 'batchSubject.batch']);

        // فلترة حسب الشعبة
        $examsQuery->when($batchId, function($q) use ($batchId) {
            $q->whereHas('batchSubject', function($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            });
        });

        // فلترة حسب الطالب
        $examsQuery->when($studentId, function($q) use ($studentId) {
            $q->whereHas('batchSubject.batch.batchStudents', function($q) use ($studentId) {
                $q->where('student_id', $studentId);
            });
        });

        $exams = $examsQuery->get();

        // تجهيز الـ response
        $response = $exams->map(function($exam) {
            return [
                'id'            => $exam->id,
                'batch_subject_id'      => $exam->batchSubject->id,
                'name'          => $exam->name,
                'exam_date'     => $exam->exam_date->format('Y-m-d'),
                'exam_time'     => $exam->exam_time,
                'exam_type'     => $exam->examType?->name ?? null,
                'total_marks'   => $exam->total_marks,
                'passing_marks' => $exam->passing_marks,
                'status'        => $exam->status,
            ];
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'تم جلب الامتحانات بنجاح',
            'data'    => $response
        ]);
    }

}
