<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Employees\Models\Employee;
use Modules\Employees\Services\CreateEmployeeUserService;
use Modules\Users\Http\Resources\UserResource;
use OpenApi\Annotations as OA;

class EmployeeActivationController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/employees/{employee}/activate-user",
     *     summary="تفعيل حساب موظف (إنشاء مستخدم مرتبط)",
     *     description="يقوم بإنشاء مستخدم من نوع 'employee' وربطه بالموظف المحدد. يُولّد معرف فريد بالشكل OEM-XXXXXXX.",
     *     tags={"Employees"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="employee",
     *         in="path",
     *         required=true,
     *         description="معرف الموظف",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الحساب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء حساب الموظف بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="الموظف غير صالح (مثل: مرتبطة بمستخدم مسبقًا)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="هذا الموظف مرتبط بحساب مستخدم بالفعل."),
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
        $employee = Employee::findOrFail($id);



        try {
            $service = new CreateEmployeeUserService();
            $user = $service->createForEmployee($employee);

            return $this->successResponse(
                new UserResource($user),
                'تم إنشاء حساب الموظف بنجاح',
                201
            );
        } catch (\DomainException $e) {
            // هذا النوع من الأخطاء خاص بالمنطق، نعرضه للمستخدم
            Log::warning('خطأ منطقي أثناء إنشاء حساب الموظف: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
            ]);
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            // أي خطأ غير متوقع يتم تسجيله بالتفاصيل
            Log::error('خطأ غير متوقع أثناء إنشاء حساب الموظف', [
                'employee_id' => $id,
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return $this->error('حدث خطأ فني أثناء إنشاء الحساب. يرجى المحاولة لاحقًا.', 500);
        }
    }
}
