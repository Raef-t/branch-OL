<?php

namespace Modules\PaymentInstallments\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PaymentInstallments\Models\PaymentInstallment;
use Modules\EnrollmentContracts\Models\EnrollmentContract; // ← إضافة للوصول إلى final_amount_usd
use Modules\PaymentInstallments\Http\Requests\StorePaymentInstallmentRequest;
use Modules\PaymentInstallments\Http\Requests\UpdatePaymentInstallmentRequest;
use Modules\PaymentInstallments\Http\Resources\PaymentInstallmentResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Illuminate\Http\JsonResponse;

class PaymentInstallmentsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/payment-installments",
     *     summary="قائمة جميع أقساط الدفع",
     *     tags={"PaymentInstallments"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع أقساط الدفع بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع أقساط الدفع بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="enrollment_contract_id", type="integer", example=1),
     *                     @OA\Property(property="installment_number", type="integer", example=1),
     *                     @OA\Property(property="due_date", type="string", format="date", example="2023-01-01"),
     *                     @OA\Property(property="planned_amount_usd", type="number", format="float", example=100.00),
     *                     @OA\Property(property="exchange_rate_at_due_date", type="number", format="float", example=10000.0000),
     *                     @OA\Property(property="planned_amount_syp", type="number", format="float", example=1000000.00),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد أقساط دفع",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي قسط دفع مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $installments = PaymentInstallment::with('enrollmentContract')
            ->orderBy('id', 'desc') // ✅ عكس الترتيب حسب الـ id
            ->get();

        if ($installments->isEmpty()) {
            return $this->error('لا يوجد أي قسط دفع مسجل حالياً', 404);
        }

        return $this->successResponse(
            PaymentInstallmentResource::collection($installments),
            'تم جلب جميع أقساط الدفع بنجاح',
            200
        );
    }
    
    /**
     * @OA\Post(
     *     path="/api/payment-installments",
     *     summary="إضافة قسط دفع جديد مع إعادة توزيع المبالغ على الأقساط السابقة (غير المدفوعة)",
     *     description="يُضاف القسط الجديد، ثم يُخصم مبلغه بالتساوي من الأقساط السابقة غير المدفوعة (حالة != paid) للحفاظ على الإجمالي الكلي للعقد. يُستخدم تقسيم دقيق (integer arithmetic) لتجنب أخطاء العشريات. إذا كانت جميع الأقساط السابقة مدفوعة، لا يُسمح بالإضافة. إذا لم يكن هناك أقساط سابقة، يُضبط المبلغ الجديد = final_amount_usd للعقد. بعد الإضافة، يُعاد ترتيب الأقساط حسب due_date.",
     *     tags={"PaymentInstallments"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"enrollment_contract_id","installment_number","due_date","planned_amount_usd","exchange_rate_at_due_date","planned_amount_syp","status"},
     *             @OA\Property(property="enrollment_contract_id", type="integer", example=1),
     *             @OA\Property(property="installment_number", type="integer", example=4, description="رقم فريد ضمن العقد"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="planned_amount_usd", type="number", format="float", example=600.00),
     *             @OA\Property(property="exchange_rate_at_due_date", type="number", format="float", example=10000.0000),
     *             @OA\Property(property="planned_amount_syp", type="number", format="float", example=6000000.00),
     *             @OA\Property(property="status", type="string", enum={"pending","paid","overdue","skipped"}, example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء قسط الدفع وإعادة التوزيع بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء قسط الدفع بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="enrollment_contract_id", type="integer", example=1),
     *                 @OA\Property(property="installment_number", type="integer", example=4),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2023-01-01"),
     *                 @OA\Property(property="planned_amount_usd", type="number", format="float", example=600.00),
     *                 @OA\Property(property="exchange_rate_at_due_date", type="number", format="float", example=10000.0000),
     *                 @OA\Property(property="planned_amount_syp", type="number", format="float", example=6000000.00),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق (مثل رقم قسط مكرر، جميع الأقساط مدفوعة، أو عدم تطابق إجمالي)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StorePaymentInstallmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $contractId = $data['enrollment_contract_id'];
        $contract = EnrollmentContract::findOrFail($contractId);

        // التحقق من عدم تكرار installment_number
        if (PaymentInstallment::where('enrollment_contract_id', $contractId)->where('installment_number', $data['installment_number'])->exists()) {
            return response()->json([
                'status' => 'error',
                'errors' => ['installment_number' => ['رقم القسط مكرر ضمن هذا العقد.']]
            ], 422);
        }

        // التحقق: إذا كانت جميع الأقساط السابقة مدفوعة، لا يُسمح بالإضافة
        $existingInstallments = PaymentInstallment::where('enrollment_contract_id', $contractId)->get();
        if ($existingInstallments->isNotEmpty() && $existingInstallments->every(function ($inst) { return $inst->status === 'paid'; })) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن إضافة قسط جديد. جميع الأقساط السابقة مدفوعة ولا يمكن التوزيع.',
                'errors' => ['installments' => ['جميع الأقساط مدفوعة.']]
            ], 422);
        }

        // إذا لم يكن هناك أقساط سابقة، اضبط planned_amount_usd = final_amount_usd للعقد
        if ($existingInstallments->isEmpty()) {
            $data['planned_amount_usd'] = $contract->final_amount_usd;
            $data['planned_amount_syp'] = $data['planned_amount_usd'] * $data['exchange_rate_at_due_date'];
        }

        // إنشاء القسط الجديد
        $newInstallment = PaymentInstallment::create($data);

        // إعادة توزيع: خصم قيمة الجديد بالتساوي من الأقساط السابقة غير المدفوعة بتقسيم دقيق
        $nonPaidExisting = PaymentInstallment::where('enrollment_contract_id', $contractId)
            ->where('id', '!=', $newInstallment->id)
            ->where('status', '!=', 'paid')
            ->orderBy('id')
            ->get();

        if ($nonPaidExisting->isNotEmpty()) {
            $totalDeductionCents = (int) ($data['planned_amount_usd'] * 100);
            $count = $nonPaidExisting->count();
            $baseDeductionCents = floor($totalDeductionCents / $count);
            $remainderCents = $totalDeductionCents % $count;

            foreach ($nonPaidExisting as $index => $existing) {
                $deductionCents = $baseDeductionCents;
                if ($index === $count - 1) {
                    $deductionCents += $remainderCents;
                }
                $deductionUsd = $deductionCents / 100;
                $newAmountUsd = round(max(0, $existing->planned_amount_usd - $deductionUsd), 2);  // لا أقل من 0
                $newAmountSyp = round($newAmountUsd * $existing->exchange_rate_at_due_date, 2);
                $existing->update([
                    'planned_amount_usd' => $newAmountUsd,
                    'planned_amount_syp' => $newAmountSyp,
                ]);
            }
        }

        // إعادة ترتيب الأقساط حسب due_date وتحديث installment_number
        $allInstallments = PaymentInstallment::where('enrollment_contract_id', $contractId)
            ->orderBy('due_date')
            ->get();
        foreach ($allInstallments as $index => $inst) {
            $inst->update(['installment_number' => $index + 1]);
        }

        // التأكد من الإجمالي
        $this->validateTotalAmount($contractId, $contract->final_amount_usd);

        return $this->successResponse(
            new PaymentInstallmentResource($newInstallment),
            'تم إنشاء قسط الدفع بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/payment-installments/{id}",
     *     summary="عرض تفاصيل قسط دفع محدد",
     *     tags={"PaymentInstallments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف قسط الدفع",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات قسط الدفع بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات قسط الدفع بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="enrollment_contract_id", type="integer", example=1),
     *                 @OA\Property(property="installment_number", type="integer", example=1),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2023-01-01"),
     *                 @OA\Property(property="planned_amount_usd", type="number", format="float", example=100.00),
     *                 @OA\Property(property="exchange_rate_at_due_date", type="number", format="float", example=10000.0000),
     *                 @OA\Property(property="planned_amount_syp", type="number", format="float", example=1000000.00),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="قسط الدفع غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="قسط الدفع غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $installment = PaymentInstallment::find($id);

        if (!$installment) {
            return $this->error('قسط الدفع غير موجود', 404);
        }

        return $this->successResponse(
            new PaymentInstallmentResource($installment),
            'تم جلب بيانات قسط الدفع بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/payment-installments/{id}",
     *     summary="تحديث بيانات قسط دفع مع إعادة توزيع المبالغ (غير المدفوعة)",
     *     description="لا يُسمح بتحديث قسط مدفوع (paid). إذا تغير planned_amount_usd، يُعاد توزيع الفرق على الأقساط الأخرى غير المدفوعة بالتساوي بتقسيم دقيق (integer) لتجنب أخطاء العشريات. لا يُسمح بتعديل المبلغ إذا كان هذا القسط الوحيد غير المدفوع أو آخر قسط.",
     *     tags={"PaymentInstallments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف قسط الدفع",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="enrollment_contract_id", type="integer", example=1),
     *             @OA\Property(property="installment_number", type="integer", example=1),
     *             @OA\Property(property="due_date", type="string", format="date", example="2023-02-01"),
     *             @OA\Property(property="planned_amount_usd", type="number", format="float", example=150.00, description="قيمة جديدة تؤدي إلى إعادة توزيع"),
     *             @OA\Property(property="exchange_rate_at_due_date", type="number", format="float", example=10000.0000),
     *             @OA\Property(property="planned_amount_syp", type="number", format="float", example=1500000.00),
     *             @OA\Property(property="status", type="string", enum={"pending","paid","overdue","skipped"}, example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات قسط الدفع وإعادة التوزيع بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات قسط الدفع بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="enrollment_contract_id", type="integer", example=1),
     *                 @OA\Property(property="installment_number", type="integer", example=1),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2023-02-01"),
     *                 @OA\Property(property="planned_amount_usd", type="number", format="float", example=150.00),
     *                 @OA\Property(property="exchange_rate_at_due_date", type="number", format="float", example=10000.0000),
     *                 @OA\Property(property="planned_amount_syp", type="number", format="float", example=1500000.00),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="قسط الدفع غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="قسط الدفع غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق (مثل قسط مدفوع، تعديل مبلغ في قسط وحيد، أو رقم مكرر)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdatePaymentInstallmentRequest $request, $id): JsonResponse
    {
        $installment = PaymentInstallment::findOrFail($id);

        // لا تسمح بتعديل قسط مدفوع
        if ($installment->status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن تعديل قسط مدفوع (paid).',
                'errors' => ['installment' => ['القسط مدفوع ولا يمكن تعديله.']]
            ], 422);
        }

        $oldAmountUsd = $installment->planned_amount_usd;
        $data = $request->validated();

        // التحقق من عدم تكرار installment_number إذا تغير
        if (isset($data['installment_number']) && $data['installment_number'] !== $installment->installment_number) {
            if (PaymentInstallment::where('enrollment_contract_id', $installment->enrollment_contract_id)
                ->where('installment_number', $data['installment_number'])
                ->where('id', '!=', $id)
                ->exists()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => ['installment_number' => ['رقم القسط مكرر ضمن هذا العقد.']]
                ], 422);
            }
        }

        // **التحقق الجديد:** لا يمكن تعديل المبلغ إذا كان هذا القسط الوحيد غير المدفوع أو آخر قسط
        if (isset($data['planned_amount_usd']) && $data['planned_amount_usd'] != $oldAmountUsd) {
            $nonPaidCount = PaymentInstallment::where('enrollment_contract_id', $installment->enrollment_contract_id)
                ->where('status', '!=', 'paid')
                ->count();
            if ($nonPaidCount <= 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لا يمكن تعديل المبلغ في قسط وحيد أو آخر قسط غير مدفوع.',
                    'errors' => ['planned_amount_usd' => ['تعديل المبلغ محظور في هذه الحالة.']]
                ], 422);
            }
        }

        // حساب planned_amount_syp إذا تغير exchange_rate
        if (isset($data['exchange_rate_at_due_date']) && isset($data['planned_amount_usd'])) {
            $data['planned_amount_syp'] = round($data['planned_amount_usd'] * $data['exchange_rate_at_due_date'], 2);
        }

        $installment->update($data);
        $newAmountUsd = $installment->fresh()->planned_amount_usd;

        $contract = $installment->enrollmentContract;
        $difference = round($newAmountUsd - $oldAmountUsd, 2);

        if ($difference != 0) {
            // إعادة توزيع الفرق على الأقساط الأخرى غير المدفوعة بتقسيم دقيق
            $otherInstallments = PaymentInstallment::where('enrollment_contract_id', $contract->id)
                ->where('id', '!=', $id)
                ->where('status', '!=', 'paid')
                ->orderBy('id')
                ->get();

            if ($otherInstallments->count() > 0) {
                $totalAdjustmentCents = (int) (abs($difference) * 100);
                $count = $otherInstallments->count();
                $baseAdjustmentCents = floor($totalAdjustmentCents / $count);
                $remainderCents = $totalAdjustmentCents % $count;
                $sign = $difference > 0 ? -1 : 1;  // خصم إذا زاد، إضافة إذا نقص

                foreach ($otherInstallments as $index => $other) {
                    $adjustmentCents = $baseAdjustmentCents;
                    if ($index === $count - 1) {
                        $adjustmentCents += $remainderCents;
                    }
                    $adjustmentUsd = ($adjustmentCents / 100) * $sign;
                    $adjustedUsd = round(max(0, $other->planned_amount_usd + $adjustmentUsd), 2);
                    $adjustedSyp = round($adjustedUsd * $other->exchange_rate_at_due_date, 2);
                    $other->update([
                        'planned_amount_usd' => $adjustedUsd,
                        'planned_amount_syp' => $adjustedSyp,
                    ]);
                }
            }
        }

        // التأكد من الإجمالي
        $this->validateTotalAmount($contract->id, $contract->final_amount_usd);

        return $this->successResponse(
            new PaymentInstallmentResource($installment->fresh()),
            'تم تحديث بيانات قسط الدفع بنجاح',
            200
        );
    }
    
    /**
     * @OA\Delete(
     *     path="/api/payment-installments/{id}",
     *     summary="حذف قسط دفع مع إعادة توزيع المبلغ على الأقساط الباقية (غير المدفوعة)",
     *     description="لا يُسمح بحذف قسط مدفوع (paid) أو قسط وحيد (لا يوجد غيره). يُحذف القسط، ثم يُضاف مبلغه بالتساوي إلى الأقساط الباقية غير المدفوعة بتقسيم دقيق (integer) لتجنب أخطاء العشريات.",
     *     tags={"PaymentInstallments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف قسط الدفع",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف قسط الدفع وإعادة التوزيع بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف قسط الدفع بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="قسط الدفع غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="قسط الدفع غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="لا يمكن حذف آخر قسط أو قسط مدفوع",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="لا يمكن حذف آخر قسط في العقد"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $installment = PaymentInstallment::findOrFail($id);

        // لا تسمح بحذف قسط مدفوع
        if ($installment->status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن حذف قسط مدفوع (paid).',
                'errors' => ['installment' => ['القسط مدفوع ولا يمكن حذفه.']]
            ], 422);
        }

        $contractId = $installment->enrollment_contract_id;
        $contract = EnrollmentContract::findOrFail($contractId);

        // التحقق: لا تحذف إذا كان آخر قسط (وحيد، لا يوجد غيره)
        $allInstallments = PaymentInstallment::where('enrollment_contract_id', $contractId)->count();
        if ($allInstallments == 1) {  // تحديث: بالضبط 1 (وحيد)
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن حذف قسط وحيد (لا يوجد غيره). يجب أن يبقى قسط واحد على الأقل.',
                'errors' => ['installment' => ['حذف قسط وحيد محظور.']]
            ], 422);
        }

        $deletedAmountUsd = $installment->planned_amount_usd;

        // حذف القسط
        $installment->delete();

        // إعادة توزيع: أضف قيمة المحذوف بالتساوي إلى الباقي غير المدفوعة بتقسيم دقيق
        $remainingInstallments = PaymentInstallment::where('enrollment_contract_id', $contractId)
            ->where('status', '!=', 'paid')
            ->orderBy('id')  // لتحديد "آخر" قسط للباقي
            ->get();

        if ($remainingInstallments->isNotEmpty()) {
            $totalAdditionCents = (int) ($deletedAmountUsd * 100);  // تحويل إلى سنتات (integer)
            $count = $remainingInstallments->count();
            $baseAdditionCents = floor($totalAdditionCents / $count);  // جزء كامل
            $remainderCents = $totalAdditionCents % $count;  // الباقي

            foreach ($remainingInstallments as $index => $remaining) {
                $additionCents = $baseAdditionCents;
                if ($index === $count - 1) {  // آخر قسط يأخذ الباقي
                    $additionCents += $remainderCents;
                }
                $additionUsd = $additionCents / 100;
                $newAmountUsd = round($remaining->planned_amount_usd + $additionUsd, 2);
                $newAmountSyp = round($newAmountUsd * $remaining->exchange_rate_at_due_date, 2);
                $remaining->update([
                    'planned_amount_usd' => $newAmountUsd,
                    'planned_amount_syp' => $newAmountSyp,
                ]);
            }
        }

        // التأكد من الإجمالي
        $this->validateTotalAmount($contractId, $contract->final_amount_usd);

        return $this->successResponse(
            null,
            'تم حذف قسط الدفع بنجاح',
            200
        );
    }
    /**
     * دالة مساعدة للتحقق من تطابق الإجمالي الكلي (مع مراعاة أن الـ paid غير متأثرة وأخطاء التقريب)
     */
    private function validateTotalAmount(int $contractId, float $expectedTotal): void
    {
        $actualTotal = PaymentInstallment::where('enrollment_contract_id', $contractId)
            ->sum('planned_amount_usd');
        if (abs($actualTotal - $expectedTotal) > 1.0) {  // تحمل أكبر (1 USD) للحالات الاستثنائية مثل paid أو تقريب في التوزيع
            throw new \Exception('خطأ في الإجمالي الكلي بعد التوزيع. المتوقع: ' . $expectedTotal . '، الفعلي: ' . $actualTotal);
        }
    }
}