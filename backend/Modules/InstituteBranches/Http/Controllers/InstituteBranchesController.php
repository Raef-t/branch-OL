<?php

namespace Modules\InstituteBranches\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\InstituteBranches\Http\Requests\InstituteBranchesStoreRequest;
use Modules\InstituteBranches\Http\Requests\InstituteBranchesUpdateRequest;
use Modules\InstituteBranches\Http\Resources\InstituteBranchResource;
use Modules\InstituteBranches\Models\InstituteBranch;
use OpenApi\Annotations as OA;

class InstituteBranchesController extends Controller
{

    use SuccessResponseTrait;
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/institute-branches",
     *     summary="قائمة الفروع",
     *     tags={"Institute Branches"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/InstituteBranchResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $branches = InstituteBranch::latest()->get();
        return $this->successResponse(
            InstituteBranchResource::collection($branches),
            'تم جلب الفروع بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/institute-branches",
     *     tags={"Institute Branches"},
     *     summary="إنشاء فرع جديد",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InstituteBranchResource")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم الإنشاء بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الفرع بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/InstituteBranchResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(InstituteBranchesStoreRequest $request)
    {
        $branch = InstituteBranch::create($request->validated());

        return $this->successResponse( 
            new InstituteBranchResource($branch),
            'تم إنشاء الفرع بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/institute-branches/{id}",
     *     summary="عرض بيانات فرع محدد",
     *     tags={"Institute Branches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الفرع",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الفرع بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/InstituteBranchResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الفرع غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Branch not found"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $branch = InstituteBranch::find($id);

        if (!$branch) {
            return $this->error('الفرع غير موجود', 404);
        }

        return $this->successResponse(
            new InstituteBranchResource($branch),
            'تم جلب بيانات الفرع بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/institute-branches/{id}",
     *     summary="تحديث بيانات فرع",
     *     tags={"Institute Branches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الفرع",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InstituteBranchResource")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم التحديث بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات الفرع بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/InstituteBranchResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الفرع غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Branch not found"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function update(InstituteBranchesUpdateRequest $request, $id)
    {
        $branch = InstituteBranch::find($id);

        if (!$branch) {
            return $this->error('الفرع غير موجود', 404);
        }

        $branch->update($request->validated());

        return $this->successResponse(
            new InstituteBranchResource($branch),
            'تم تحديث بيانات الفرع بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/institute-branches/{id}",
     *     summary="حذف فرع",
     *     tags={"Institute Branches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الفرع",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم الحذف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الفرع بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الفرع غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Branch not found"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $branch = InstituteBranch::find($id);

        if (!$branch) {
            return $this->error('الفرع غير موجود', 404);
        }

        $branch->delete();

        return $this->successResponse(
            null,
            'تم حذف الفرع بنجاح',
            200
        );
    }
}
