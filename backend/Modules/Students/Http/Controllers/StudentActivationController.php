<?php

namespace Modules\Students\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Students\Models\Student;
use Modules\Students\Services\CreateStudentUserService;
use Modules\Users\Http\Resources\UserResource;
use OpenApi\Annotations as OA;

class StudentActivationController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/students/{student}/activate-user",
     *     summary="تفعيل حساب طالب (إنشاء مستخدم مرتبط)",
     *     description="يقوم بإنشاء مستخدم من نوع 'student' وربطه بالطالب المحدد. يُولّد معرف فريد بالشكل OST-XXXXXXX.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الحساب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء حساب الطالب بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="الطالب غير صالح (مثل: مرتبطة بمستخدم مسبقًا)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="هذا الطالب مرتبط بحساب مستخدم بالفعل."),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الطالب غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطأ فني",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ فني أثناء إنشاء الحساب. يرجى المحاولة لاحقًا."),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function __invoke($id)
    {
        try {

            $student = Student::find($id);

            if (!$student) {
                return $this->error('الطالب غير موجود.', 404);
            }

            $service = new CreateStudentUserService();
            $user = $service->createForStudent($student);

            return $this->successResponse(
                new UserResource($user),
                'تم إنشاء حساب الطالب بنجاح',
                201
            );
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->error('حدث خطأ فني أثناء إنشاء الحساب. يرجى المحاولة لاحقًا.', 500);
        }
    }
}
