<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\Employees\Http\Requests\StoreEmployeeForBatchRequest;
use Modules\Employees\Models\Employee;
use Modules\Employees\Http\Requests\StoreEmployeeRequest;
use Modules\Employees\Http\Requests\UpdateEmployeeRequest;
use Modules\Employees\Http\Resources\EmployeeBatchAssignmentResource;
use Modules\Employees\Http\Resources\EmployeeResource;
use Modules\Enrollments\Services\FileUploadService;
use Modules\Shared\Traits\SuccessResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Batches\Models\BatchEmployee;
use Modules\Batches\Models\Batch;

class EmployeesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/employees",
     *     summary="قائمة جميع الموظفين",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع الموظفين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع الموظفين بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1, nullable=true),
     *                     @OA\Property(property="first_name", type="string", example="أحمد"),
     *                     @OA\Property(property="last_name", type="string", example="محمد", nullable=true),
     *                     @OA\Property(property="job_title", type="string", example="مشرف دورات", nullable=true),
     *                     @OA\Property(property="job_type", type="string", example="supervisor"),
     *                     @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15"),
     *                     @OA\Property(property="phone", type="string", example="+963123456789", nullable=true),
     *                     @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="institute_branch",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="فرع دمشق"),
     *                         @OA\Property(property="address", type="string", example="دمشق، شارع فيصل"),
     *                         @OA\Property(property="code", type="string", example="DM1"),
     *                         @OA\Property(property="phone", type="string", example="0111234567"),
     *                         @OA\Property(property="email", type="string", example="damascus@example.com")
     *                     ),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد موظفين",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي موظف مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $employees = Employee::withoutGlobalScopes()
            ->with(['instituteBranch', 'user.roles'])
            ->orderBy('id', 'desc')
            ->get();

        if ($employees->isEmpty()) {
            return $this->successResponse(
                EmployeeResource::collection([]),
                'لا يوجد أي موظف مسجل حالياً',
                200
            );
        }

        return $this->successResponse(
            EmployeeResource::collection($employees),
            'تم جلب جميع الموظفين بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/employees",
     *     summary="إضافة موظف جديد",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","job_type","hire_date","institute_branch_id"},
     *             @OA\Property(property="user_id", type="integer", example=1, nullable=true),
     *             @OA\Property(property="first_name", type="string", example="أحمد"),
     *             @OA\Property(property="last_name", type="string", example="محمد", nullable=true),
     *             @OA\Property(property="job_title", type="string", example="مشرف دورات", nullable=true),
     *             @OA\Property(property="job_type", type="string", example="supervisor"),
     *             @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15"),
     *             @OA\Property(property="phone", type="string", example="+963123456789", nullable=true),
     *             @OA\Property(property="institute_branch_id", type="integer", example=1),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الموظف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الموظف بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="first_name", type="string", example="أحمد"),
     *                 @OA\Property(property="last_name", type="string", example="محمد", nullable=true),
     *                 @OA\Property(property="job_title", type="string", example="مشرف دورات", nullable=true),
     *                 @OA\Property(property="job_type", type="string", example="supervisor"),
     *                 @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15"),
     *                 @OA\Property(property="phone", type="string", example="+963123456789", nullable=true),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="institute_branch",
     *                     type="object",
     *                     nullable=true,
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="فرع دمشق"),
     *                     @OA\Property(property="address", type="string", example="دمشق، شارع فيصل"),
     *                     @OA\Property(property="code", type="string", example="DM1"),
     *                     @OA\Property(property="phone", type="string", example="0111234567"),
     *                     @OA\Property(property="email", type="string", example="damascus@example.com")
     *                 ),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreEmployeeRequest $request)
    {

        $employee = Employee::create($request->validated());
        $employee->load('instituteBranch');

        return $this->successResponse(
            new EmployeeResource($employee),
            'تم إنشاء الموظف بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/employees/{id}",
     *     summary="عرض تفاصيل موظف محدد",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الموظف",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات الموظف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الموظف بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="first_name", type="string", example="أحمد"),
     *                 @OA\Property(property="last_name", type="string", example="محمد", nullable=true),
     *                 @OA\Property(property="job_title", type="string", example="مشرف دورات", nullable=true),
     *                 @OA\Property(property="job_type", type="string", example="supervisor"),
     *                 @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15"),
     *                 @OA\Property(property="phone", type="string", example="+963123456789", nullable=true),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="institute_branch",
     *                     type="object",
     *                     nullable=true,
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="فرع دمشق"),
     *                     @OA\Property(property="address", type="string", example="دمشق، شارع فيصل"),
     *                     @OA\Property(property="code", type="string", example="DM1"),
     *                     @OA\Property(property="phone", type="string", example="0111234567"),
     *                     @OA\Property(property="email", type="string", example="damascus@example.com")
     *                 ),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الموظف غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الموظف غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $employee = Employee::withoutGlobalScopes()->with('instituteBranch')->find($id);

        if (!$employee) {
            return $this->error('الموظف غير موجود', 404);
        }

        return $this->successResponse(
            new EmployeeResource($employee),
            'تم جلب بيانات الموظف بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/employees/{id}",
     *     summary="تحديث بيانات موظف",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الموظف",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=2, nullable=true),
     *             @OA\Property(property="first_name", type="string", example="محمد"),
     *             @OA\Property(property="last_name", type="string", example="علي", nullable=true),
     *             @OA\Property(property="job_title", type="string", example="منسق دورات", nullable=true),
     *             @OA\Property(property="job_type", type="string", example="coordinator"),
     *             @OA\Property(property="hire_date", type="string", format="date", example="2023-02-15"),
     *             @OA\Property(property="phone", type="string", example="+963987654321", nullable=true),
     *             @OA\Property(property="institute_branch_id", type="integer", example=2),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات الموظف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات الموظف بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=2, nullable=true),
     *                 @OA\Property(property="first_name", type="string", example="محمد"),
     *                 @OA\Property(property="last_name", type="string", example="علي", nullable=true),
     *                 @OA\Property(property="job_title", type="string", example="منسق دورات", nullable=true),
     *                 @OA\Property(property="job_type", type="string", example="coordinator"),
     *                 @OA\Property(property="hire_date", type="string", format="date", example="2023-02-15"),
     *                 @OA\Property(property="phone", type="string", example="+963987654321", nullable=true),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=2),
     *                 @OA\Property(
     *                     property="institute_branch",
     *                     type="object",
     *                     nullable=true,
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="فرع حلب"),
     *                     @OA\Property(property="address", type="string", example="حلب، شارع الجامعة"),
     *                     @OA\Property(property="code", type="string", example="HL2"),
     *                     @OA\Property(property="phone", type="string", example="0211234567"),
     *                     @OA\Property(property="email", type="string", example="haleb@example.com")
     *                 ),
     *                 @OA\Property(property="is_active", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الموظف غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الموظف غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = Employee::withoutGlobalScopes()->find($id);

        if (!$employee) {
            return $this->error('الموظف غير موجود', 404);
        }

        $employee->update($request->validated());
        $employee->load('instituteBranch');

        return $this->successResponse(
            new EmployeeResource($employee),
            'تم تحديث بيانات الموظف بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/employees/{id}",
     *     summary="حذف موظف",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الموظف",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف الموظف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الموظف بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الموظف غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الموظف غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $employee = Employee::withoutGlobalScopes()->find($id);

        if (!$employee) {
            return $this->error('الموظف غير موجود', 404);
        }

        $employee->delete();

        return $this->successResponse(
            null,
            'تم حذف الموظف بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/employees/{id}/photo",
     *     summary="رفع أو تحديث صورة الموظف",
     *     description="يُستخدم هذا المسار لرفع أو تحديث صورة موظف معين. يتم حفظ الصورة في مجلد employees/photos في التخزين العام.",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الموظف الذي سيتم رفع صورته",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="صورة الموظف المراد رفعها",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"photo"},
     *                 @OA\Property(
     *                     property="photo",
     *                     type="string",
     *                     format="binary",
     *                     description="ملف الصورة (JPEG, PNG)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم رفع الصورة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم رفع صورة الموظف بنجاح."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="photo_url", type="string", example="https://example.com/storage/employees/photos/abc123.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الموظف غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الموظف غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="يجب إرسال صورة صحيحة."),
     *             @OA\Property(property="errors", type="object", example={"photo": {"الملف المطلوب غير صالح"}})
     *         )
     *     )
     * )
     */
    public function uploadPhoto(Request $request, FileUploadService $uploader, $id)
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->error('الموظف غير موجود', 404);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photo.required' => 'يجب اختيار صورة.',
            'photo.image' => 'الملف يجب أن يكون صورة.',
            'photo.mimes' => 'يجب أن تكون الصورة بصيغة jpeg أو png أو jpg.',
            'photo.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميغابايت.',
        ]);

        // 🖼️ رفع الصورة باستخدام الخدمة
        $path = $uploader->uploadEmployeePhoto($request->file('photo'));

        // 🧹 حذف الصورة القديمة إن وجدت (اختياري)
        if ($employee->photo_path && Storage::disk('public')->exists($employee->photo_path)) {
            Storage::disk('public')->delete($employee->photo_path);
        }

        // 💾 تحديث السجل
        $employee->update(['photo_path' => $path]);

        return $this->successResponse([
            'photo_url' => asset('storage/' . $path),
        ], 'تم رفع صورة الموظف بنجاح.', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/employees/assign-to-batch",
     *     summary="إنشاء موظف جديد وتخصيصه لدورة معينة",
     *     description="يتم إنشاء موظف جديد ثم تحديث جميع batch_subjects الخاصة بالدورة (batch) بـ employee_id الجديد",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","job_type","hire_date","institute_branch_id","batch_id"},
     *             @OA\Property(property="first_name", type="string", example="أحمد"),
     *             @OA\Property(property="last_name", type="string", nullable=true, example="محمد"),
     *             @OA\Property(property="job_title", type="string", nullable=true, example="مشرف دورات"),
     *             @OA\Property(property="job_type", type="string", example="supervisor"),
     *             @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+963123456789"),
     *             @OA\Property(property="institute_branch_id", type="integer", example=1),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="batch_id", type="integer", example=1, description="معرف الدورة للتخصيص")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الموظف وتخصيصه للدورة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الموظف وتخصيصه للدورة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="employee", 
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="أحمد"),
     *                     @OA\Property(property="last_name", type="string", nullable=true, example="محمد"),
     *                     @OA\Property(property="job_title", type="string", nullable=true, example="مشرف دورات"),
     *                     @OA\Property(property="job_type", type="string", example="supervisor"),
     *                     @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15"),
     *                     @OA\Property(property="phone", type="string", nullable=true, example="+963123456789"),
     *                     @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="institute_branch",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="فرع دمشق"),
     *                         @OA\Property(property="address", type="string", example="دمشق، شارع فيصل"),
     *                         @OA\Property(property="code", type="string", example="DM1"),
     *                         @OA\Property(property="phone", type="string", example="0111234567"),
     *                         @OA\Property(property="email", type="string", example="damascus@example.com")
     *                     ),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 ),
     *                 @OA\Property(property="updated_batch_subjects_count", type="integer", example=3, description="عدد batch_subjects التي تم تحديثها")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الدورة غير موجودة أو لا توجد مواد لها",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الدورة غير موجودة أو لا توجد مواد مرتبطة بها"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function storeForBatch(StoreEmployeeForBatchRequest $request)
    {
        // استخراج بيانات الموظف (بدون batch_id)
        $employeeData = $request->validated();
        unset($employeeData['batch_id']);

        // إنشاء الموظف الجديد
        $employee = Employee::create($employeeData);

        // البحث عن batch_subjects الخاصة بالدورة وتحديث employee_id
        $batchSubjects = BatchSubject::where('batch_id', $request->batch_id)->get();

        if ($batchSubjects->isEmpty()) {
            // حذف الموظف إذا لم توجد batch_subjects (اختياري، لتجنب إنشاء موظف غير مستخدم)
            $employee->delete();
            return $this->error('الدورة غير موجودة أو لا توجد مواد مرتبطة بها', 404);
        }

        $updatedCount = BatchSubject::where('batch_id', $request->batch_id)
            ->update(['employee_id' => $employee->id]);

        return $this->successResponse(
            new EmployeeBatchAssignmentResource([
                'employee' => $employee,
                'updated_batch_subjects_count' => $updatedCount,
            ]),
            'تم إنشاء الموظف وتخصيصه للدورة بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/employees/employees-with-batches",
     *     summary="جلب جميع الموظفين مع الدورات المخصصة لهم",
     *     description="يتم جلب قائمة بجميع الموظفين مع تفاصيل الدورات (batches) المرتبطة بهم عبر batch_subjects، مع تحميل بيانات الفرع.",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع الموظفين مع الدورات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع الموظفين مع الدورات"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="أحمد"),
     *                     @OA\Property(property="last_name", type="string", example="محمد", nullable=true),
     *                     @OA\Property(property="job_title", type="string", example="مشرف دورات", nullable=true),
     *                     @OA\Property(property="job_type", type="string", example="supervisor"),
     *                     @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15"),
     *                     @OA\Property(property="phone", type="string", example="+963123456789", nullable=true),
     *                     @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="batches",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1, nullable=true),
     *                             @OA\Property(property="name", type="string", example="دورة برمجة متقدمة", nullable=true)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد موظفين",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي موظف مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function allWithBatches()
    {
        $employees = Employee::with(['BatchSubjects.batch' => function ($query) {
            $query->select('id', 'name'); // نجيب فقط id و name للـ batch
        }])->get();

        $data = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'job_title' => $employee->job_title,
                'job_type' => $employee->job_type,
                'hire_date' => $employee->hire_date,
                'phone' => $employee->phone,
                'institute_branch_id' => $employee->institute_branch_id,
                'is_active' => $employee->is_active,
                'batches' => $employee->BatchSubjects->map(function ($bs) {
                    return [
                        'id' => $bs->batch->id ?? null,
                        'name' => $bs->batch->name ?? null
                    ];
                }),
            ];
        });

        return $this->successResponse($data, 'تم جلب جميع الموظفين مع الدورات', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/employees/{id}/assign-to-batch",
     *     summary="تعيين موظف موجود على دفعة (أو تحديث تعيينه) عبر جدول batch_employees",
     *     description="
هذا المسار مخصّص لإدارة علاقة التعيين بين موظف ودفعة في جدول batch_employees.

💡 **ما الذي يفعله هذا المسار؟**
- إذا كان الموظف معيَّنًا مسبقًا على هذه الدفعة (سجل موجود في batch_employees بنفس combination: batch_id + employee_id):
  - يتم **تحديث** السجل الموجود (role, assignment_date, notes, is_active ...).
- إذا لم يكن معيَّنًا:
  - يتم **إنشاء** سجل تعيين جديد في batch_employees.

🔒 **ما لا يفعله هذا المسار:**
- لا يقوم بإنشاء موظف جديد.
- لا يقوم بتعديل بيانات الموظف الأساسية (الاسم، المسمى الوظيفي، الفرع ...).
- لا يقوم بإنشاء دفعة جديدة.

📥 **الحقول في الـ Request:**
- `batch_id` (إلزامي):
  - يحدد الدفعة التي سيتم تعيين الموظف عليها.
  - يجب أن تكون الدفعة موجودة في جدول batches.

- `role` (اختياري):
  - يحدد الدور الوظيفي للموظف داخل هذه الدفعة (مثل: supervisor, coordinator, ...).
  - إذا لم يُرسل، سيتم اعتباره تلقائيًا: `supervisor`.

- `assignment_date` (اختياري):
  - تاريخ التعيين على الدفعة.
  - إذا لم يُرسل، سيتم استخدام تاريخ اليوم (`today`) تلقائيًا.

- `assigned_by` (اختياري):
  - معرّف المستخدم (user_id) الذي قام بالتعيين.
  - إذا لم يُرسل، سيستخدم النظام المستخدم الحالي من `Auth::id()` (إن وجد).

- `notes` (اختياري):
  - ملاحظات نصية حرة حول هذا التعيين.

- `assignment_is_active` (اختياري):
  - حالة تفعيل التعيين على الدفعة.
  - إذا لم يُرسل، سيتم اعتباره تلقائيًا `true`.

⚠ **ملاحظات حول السلوك:**
- هذا المسار آمن لإعادة الاستدعاء (idempotent) على مستوى combination (employee_id + batch_id):
  - استدعاؤه مرة ثانية بنفس batch_id لن ينشئ تعيينًا مكررًا، بل سيحدّث نفس السجل.
- في حال إرسال قيم جديدة للحقول (مثل role, notes)، ستُحدث على السجل السابق.
",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرّف الموظف الذي سيتم تعيينه على الدفعة. يجب أن يكون الموظف موجودًا مسبقًا في جدول employees.",
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="batch_id",
     *                 type="integer",
     *                 example=2,
     *                 description="معرّف الدفعة المراد تعيين الموظف عليها. هذا الحقل إلزامي، ويجب أن يكون موجودًا في جدول batches."
     *             ),
     *             @OA\Property(
     *                 property="role",
     *                 type="string",
     *                 nullable=true,
     *                 example="supervisor",
     *                 description="دور الموظف داخل الدفعة (مثل: supervisor, coordinator ...). إذا لم يُرسل، سيتم تخزين القيمة الافتراضية supervisor."
     *             ),
     *             @OA\Property(
     *                 property="assignment_date",
     *                 type="string",
     *                 format="date",
     *                 nullable=true,
     *                 example="2025-01-10",
     *                 description="تاريخ التعيين على الدفعة. إذا لم يُرسل، سيستخدم النظام تاريخ اليوم تلقائيًا."
     *             ),
     *             @OA\Property(
     *                 property="assigned_by",
     *                 type="integer",
     *                 nullable=true,
     *                 example=1,
     *                 description="معرّف المستخدم الذي قام بالتعيين (user_id من جدول users). إذا لم يُرسل، سيستخدم النظام Auth::id() (المستخدم الحالي) إن وجد."
     *             ),
     *             @OA\Property(
     *                 property="notes",
     *                 type="string",
     *                 nullable=true,
     *                 example="تم تعيين الموظف كمشرف رئيسي على الدفعة بناءً على قرار الإدارة.",
     *                 description="ملاحظات اختيارية تُخزن مع التعيين في جدول batch_employees."
     *             ),
     *             @OA\Property(
     *                 property="assignment_is_active",
     *                 type="boolean",
     *                 nullable=true,
     *                 example=true,
     *                 description="حالة تفعيل التعيين على الدفعة. إذا لم يُرسل، سيتم اعتباره true تلقائيًا."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم إنشاء أو تحديث التعيين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="تم تعيين الموظف على الدفعة / تحديث تعيينه بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="employee",
     *                     type="object",
     *                     description="بيانات الموظف الأساسية (للعرض فقط، لا يتم تعديلها من هذا المسار).",
     *                     @OA\Property(property="id", type="integer", example=7),
     *                     @OA\Property(property="first_name", type="string", example="محمد"),
     *                     @OA\Property(property="last_name", type="string", example="علي"),
     *                     @OA\Property(property="job_title", type="string", example="مشرف"),
     *                     @OA\Property(property="job_type", type="string", example="supervisor"),
     *                     @OA\Property(property="institute_branch_id", type="integer", example=2),
     *                     @OA\Property(property="is_active", type="boolean", example=true)
     *                 ),
     *                 @OA\Property(
     *                     property="assignment",
     *                     type="object",
     *                     description="سجل التعيين في جدول batch_employees بعد الإنشاء أو التحديث.",
     *                     @OA\Property(property="id", type="integer", example=12),
     *                     @OA\Property(property="batch_id", type="integer", example=2),
     *                     @OA\Property(property="employee_id", type="integer", example=7),
     *                     @OA\Property(property="role", type="string", example="supervisor"),
     *                     @OA\Property(property="assignment_date", type="string", format="date", example="2025-01-10"),
     *                     @OA\Property(property="assigned_by", type="integer", example=1),
     *                     @OA\Property(property="notes", type="string", example="تم التعيين بناءً على طلب الإدارة"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-10T09:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-10T09:15:00Z")
     *                 ),
     *                 @OA\Property(
     *                     property="assigned_batch_id",
     *                     type="integer",
     *                     example=2,
     *                     description="معرّف الدفعة التي تم تعيين الموظف عليها في هذه العملية."
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الموظف أو الدفعة غير موجودين",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الموظف غير موجود أو الدفعة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من صحة البيانات المرسلة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ غير متوقع في الخادم أثناء تنفيذ عملية التعيين",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء ربط الموظف بالدفعة")
     *         )
     *     )
     * )
     */


    public function updateAssignmentWithPost(StoreEmployeeForBatchRequest $request, $id)
    {
        // 🧑‍💼 1) التأكد من وجود الموظف
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->error('الموظف غير موجود', 404);
        }

        // ✅ 2) بيانات التعيين بعد الـ validation (من StoreEmployeeForBatchRequest)
        $data = $request->validated();

        // 🧾 batch_id الآن مطلوب في الريكويست، وتحققنا منه أنه exists في القاعدة
        $batch = Batch::find($data['batch_id']);

        if (! $batch) {
            // هذا احتياط إضافي، مع أن الـ Request يتأكد من وجوده
            return $this->error('الدفعة غير موجودة', 404);
        }

        DB::beginTransaction();

        try {
            // 🧩 3) إنشاء أو تحديث سجل التعيين في batch_employees
            $assignment = BatchEmployee::updateOrCreate(
                [
                    'batch_id'    => $batch->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'role'            => $data['role'] ?? 'supervisor',
                    'assignment_date' => $data['assignment_date'] ?? now()->toDateString(),
                    'assigned_by'     => $data['assigned_by'] ?? Auth::id(),
                    'notes'           => $data['notes'] ?? null,
                    'is_active'       => $data['assignment_is_active'] ?? true,
                ]
            );

            DB::commit();

            $payload = [
                'employee'          => $employee,        // فقط للعرض إن أحببت
                'assignment'        => $assignment,      // سجل الربط في batch_employees
                'assigned_batch_id' => $batch->id,
            ];

            return (new EmployeeBatchAssignmentResource($payload))
                ->additional([
                    'status'  => 'success',
                    'message' => 'تم تعيين الموظف على الدفعة / تحديث تعيينه بنجاح',
                ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->error('حدث خطأ غير متوقع أثناء ربط الموظف بالدفعة', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/employees/{id}/assignments/{batch_id}",
     *     summary="حذف التعيين بين موظف ودفعة محددة عبر جدول batch_employees",
     *     description="
هذا المسار يقوم بحذف علاقة التعيين بين موظف ودفعة في جدول batch_employees.

🔍 **ما الذي يفعله هذا المسار؟**
- يحذف سجل التعيين الذي يربط موظفًا بدفعة معينة (employee_id + batch_id).
- لا يحذف الموظف نفسه.
- لا يحذف الدفعة نفسها.
- لا يؤثر على تعيينات أخرى لنفس الموظف على دفعات مختلفة.

⚠ **ماذا يحدث إذا كان السجل غير موجود؟**
- يعيد خطأ 404 برسالة: (لا يوجد تعيين مسبق لهذا الموظف على هذه الدفعة).

🔐 **الصلاحيات:**
- يتطلب توثيق sanctum.
",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرّف الموظف",
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="path",
     *         required=true,
     *         description="معرّف الدفعة المراد حذف التعيين منها",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف التعيين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="تم حذف التعيين بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="employee_id", type="integer", example=7),
     *                 @OA\Property(property="batch_id", type="integer", example=3)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الموظف أو الدفعة أو التعيين غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد تعيين مسبق لهذا الموظف على هذه الدفعة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ غير متوقع أثناء عملية الحذف",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء حذف التعيين")
     *         )
     *     )
     * )
     */

    public function deleteBatchAssignment($id, $batch_id)
    {
        // 1) التأكد من وجود الموظف
        $employee = Employee::find($id);
        if (! $employee) {
            return $this->error('الموظف غير موجود', 404);
        }

        // 2) التأكد من وجود الدفعة
        $batch = Batch::find($batch_id);
        if (! $batch) {
            return $this->error('الدفعة غير موجودة', 404);
        }

        // 3) البحث عن سجل التعيين
        $assignment = BatchEmployee::where('employee_id', $id)
            ->where('batch_id', $batch_id)
            ->first();

        if (! $assignment) {
            return $this->error('لا يوجد تعيين مسبق لهذا الموظف على هذه الدفعة', 404);
        }

        // 4) حذف السجل
        $assignment->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'تم حذف التعيين بنجاح',
            'data'    => [
                'employee_id' => $id,
                'batch_id'    => $batch_id,
            ]
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/employees/with-assignments",
     *     summary="جلب جميع الموظفين مع سجلات تعيينهم على الدفعات (إن وجدت)",
     *     description="
يعيد هذا المسار جميع الموظفين في النظام، مع إرفاق
سجلات التعيين من جدول `batch_employees` عند وجودها.

💡 **السلوك:**
- يتم جلب جميع الموظفين من جدول `employees`.
- يتم إرفاق سجل أو أكثر من سجلات التعيين من جدول `batch_employees`.
- كل سجل تعيين يحتوي على بيانات الدفعة المرتبطة به.
- في حال عدم وجود تعيينات لموظف معيّن، تعود مصفوفة `batch_assignments` فارغة.

🔒 **قيود:**
- المسار للعرض فقط (Read-only).
- لا يقوم بإنشاء أو تعديل أي بيانات.
",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الموظفين مع التعيينات بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="تم جلب الموظفين مع بيانات التعيين والدفعة المرتبطة"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=7),
     *                     @OA\Property(property="first_name", type="string", example="محمد"),
     *                     @OA\Property(property="last_name", type="string", example="علي"),
     *                     @OA\Property(property="job_title", type="string", example="مشرف"),
     *                     @OA\Property(property="job_type", type="string", example="supervisor"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *
     *                     @OA\Property(
     *                         property="institute_branch",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="فرع دمشق")
     *                     ),
     *
     *                     @OA\Property(
     *                         property="batch_assignments",
     *                         type="array",
     *                         description="سجلات التعيين الخاصة بالموظف",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=12),
     *                             @OA\Property(property="batch_id", type="integer", example=3),
     *                             @OA\Property(property="role", type="string", example="supervisor"),
     *                             @OA\Property(property="assignment_date", type="string", format="date", example="2025-01-10"),
     *                             @OA\Property(property="is_active", type="boolean", example=true),
     *                             @OA\Property(property="notes", type="string", nullable=true, example="مشرف أساسي"),
     *
     *                             @OA\Property(
     *                                 property="batch",
     *                                 type="object",
     *                                 nullable=true,
     *                                 description="بيانات الدفعة المرتبطة بهذا التعيين",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="دفعة بكالوريا علمي 2025")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function indexWithAssignments()
    {
        $employees = Employee::with([
            'instituteBranch',
            'batchAssignments.batch' // هنا بيت القصيد
        ])->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'تم جلب الموظفين مع بيانات التعيين والدفعة المرتبطة',
            'data'    => $employees,
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/employees/count",
     *     summary="إرجاع عدد الموظفين",
     *     description="يعيد عدد جميع الموظفين الفعّالين في النظام",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب عدد الموظفين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب عدد الموظفين بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_employees", type="integer", example=42)
     *             )
     *         )
     *     )
     * )
     */
    public function count()
    {
        $count = Employee::count();

        return $this->successResponse(
            [
                'total_employees' => $count,
            ],
            'تم جلب عدد الموظفين بنجاح',
            200
        );
    }
}
