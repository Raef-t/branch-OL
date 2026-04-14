<?php

namespace Modules\Families\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Families\Http\Requests\FamiliesStoreRequest;
use Modules\Families\Http\Requests\FamiliesUpdateRequest;
use Modules\Families\Http\Resources\FamilyResource;
use Modules\Families\Models\Family;
use OpenApi\Annotations as OA;

class FamiliesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/families",
     *     summary="قائمة جميع العائلات",
     *     tags={"Families"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب القائمة بنجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/FamilyResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $families = Family::with('user')->latest()->get();
        return $this->successResponse(
            FamilyResource::collection($families),
            'تم جلب العائلات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/families",
     *     summary="إنشاء عائلة جديدة",
     *     tags={"Families"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         description="بيانات العائلة (user_id اختياري)",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=123, nullable=true, description="معرف المستخدم (اختياري)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم الإنشاء بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء العائلة بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/FamilyResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من البيانات",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(FamiliesStoreRequest $request)
    {
        $family = Family::create($request->validated());

        if ($family->user_id) {
            $family->load('user');
        }

        return $this->successResponse(
            new FamilyResource($family),
            'تم إنشاء العائلة بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/families/{id}",
     *     summary="عرض بيانات عائلة محددة",
     *     tags={"Families"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف العائلة",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم العرض بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات العائلة بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/FamilyResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="العائلة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="العائلة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $family = Family::with(['user', 'students.user', 'guardians'])->find($id);

        if (!$family) {
            return $this->error('العائلة غير موجودة', 404);
        }

        return $this->successResponse(
            new FamilyResource($family),
            'تم جلب بيانات العائلة بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/families/{id}",
     *     summary="تحديث بيانات عائلة",
     *     tags={"Families"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف العائلة",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=456, nullable=true, description="يمكن تغييره أو جعله null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم التحديث بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات العائلة بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/FamilyResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="العائلة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="العائلة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق (مثل: user_id مستخدم من قبل)",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function update(FamiliesUpdateRequest $request, $id)
    {
        $family = Family::find($id);

        if (!$family) {
            return $this->error('العائلة غير موجودة', 404);
        }

        $family->update($request->validated());

        if ($family->user_id) {
            $family->load('user');
        }

        return $this->successResponse(
            new FamilyResource($family),
            'تم تحديث بيانات العائلة بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/families/{id}",
     *     summary="حذف عائلة",
     *     tags={"Families"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف العائلة",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم الحذف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف العائلة بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="العائلة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="العائلة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="لا يمكن الحذف لأن العائلة مرتبطة بطلاب",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يمكن حذف العائلة لأنها مرتبطة بطلاب"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $family = Family::find($id);

        if (!$family) {
            return $this->error('العائلة غير موجودة', 404);
        }

        // منع الحذف إذا كانت مرتبطة بطلاب (لأن students.family_id ليس nullable)
        if ($family->students()->count() > 0) {
            return $this->error('لا يمكن حذف العائلة لأنها مرتبطة بطلاب', 400);
        }

        $family->delete();

        return $this->successResponse(
            null,
            'تم حذف العائلة بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/families/me/financial-summary",
     *     summary="جلب الملخص المالي لعائلة المستخدم الحالي",
     *     description="يعيد ملخصًا ماليًا للعائلة المرتبطة بالمستخدم الحالي (إجمالي المستحق، المدفوع، والمتبقي)، مع قائمة الطلاب.",
     *     tags={"Families"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الملخص المالي بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الملخص المالي للعائلة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="family_id", type="integer", example=2),
     *                 @OA\Property(
     *                     property="students",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="student_id", type="integer", example=15),
     *                         @OA\Property(property="student_name", type="string", example="أحمد محمد")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="financial_summary",
     *                     type="object",
     *                     @OA\Property(property="total_due_usd", type="number", format="float", example=150.00),
     *                     @OA\Property(property="total_paid_usd", type="number", format="float", example=100.00),
     *                     @OA\Property(property="remaining_usd", type="number", format="float", example=50.00)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="المستخدم لا يملك عائلة مرتبطة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="هذا الحساب لا يملك عائلة")
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
    public function myFamilyFinancialSummary()
    {
        $user = Auth::user();
        // تأكيد إنو المستخدم عائلة
        if (!$user->family) {
            return response()->json([
                'message' => 'هذا الحساب لا يملك عائلة'
            ], 403);
        }

        $family = $user->family()->with([
            'students.latestActiveEnrollmentContract'
        ])->first();

        $studentsData = [];
        $totalDue = 0;
        $totalPaid = 0;

        foreach ($family->students as $student) {
            $contract = $student->latestActiveEnrollmentContract;

            $studentsData[] = [
                'student_id'   => $student->id,
                'student_name' => $student->full_name,
            ];

            if ($contract) {
                $totalDue  += $contract->final_amount_usd;
                $totalPaid += $contract->paid_amount_usd;
            }
        }

        return $this->successResponse(
            [
                'family_id' => $family->id,
                'students' => $studentsData,
                'financial_summary' => [
                    'total_due_usd'  => round($totalDue, 2),
                    'total_paid_usd' => round($totalPaid, 2),
                    'remaining_usd'  => round($totalDue - $totalPaid, 2),
                ],
            ],
            'تم جلب الملخص المالي للعائلة بنجاح',
            200
        );

    }
}