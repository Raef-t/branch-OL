<?php

namespace Modules\EnrollmentContracts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EnrollmentContracts\Models\EnrollmentContract;
use Modules\EnrollmentContracts\Http\Requests\StoreEnrollmentContractRequest;
use Modules\EnrollmentContracts\Http\Requests\UpdateEnrollmentContractRequest;
use Modules\EnrollmentContracts\Http\Resources\EnrollmentContractResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\PaymentInstallments\Models\PaymentInstallment;
use Modules\Payments\Http\Requests\StorePaymentRequest;
use Modules\Payments\Models\Payment;
use Modules\Payments\Services\PaymentService;
use Illuminate\Support\Facades\Validator;

class EnrollmentContractsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/enrollment-contracts",
     *     summary="قائمة جميع عقود التسجيل",
     *     tags={"EnrollmentContracts"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع عقود التسجيل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع عقود التسجيل بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="student_id", type="integer", example=1),
     *                     @OA\Property(property="total_amount_usd", type="number", format="float", example=1000.00),
     *                     @OA\Property(property="discount_percentage", type="number", format="float", example=10.00),
     *                     @OA\Property(property="discount_amount", type="number", format="float", example=100.00),
     *                     @OA\Property(property="final_amount_usd", type="number", format="float", example=900.00),
     *                     @OA\Property(property="exchange_rate_at_enrollment", type="number", format="float", example=15000.0000),
     *                     @OA\Property(property="final_amount_syp", type="number", format="float", example=13500000.00),
     *                     @OA\Property(property="agreed_at", type="string", format="date", example="2023-01-15"),
     *                     @OA\Property(property="installments_start_date", type="string", format="date", example="2023-01-15", nullable=true),
     *                     @OA\Property(property="description", type="string", example="يشمل الكتب والرسوم الإدارية"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد عقود تسجيل",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي عقد تسجيل مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $enrollmentContracts = EnrollmentContract::all();

        if ($enrollmentContracts->isEmpty()) {
            return $this->error('لا يوجد أي عقد تسجيل مسجل حالياً', 404);
        }

        return $this->successResponse(
            EnrollmentContractResource::collection($enrollmentContracts),
            'تم جلب جميع عقود التسجيل بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/enrollment-contracts",
     *     summary="إنشاء عقد تسجيل جديد مع نظام أقساط",
     *     description="
     * ينشئ هذا المسار عقد تسجيل جديد لطالب، مع دعم العمل بعملة الدولار أو الليرة السورية،
     * وإنشاء الأقساط بإحدى طريقتين:
     *
     * 🔹 **العمل بالعملة:**
     * - يمكن إدخال المبلغ النهائي بالدولار فقط.
     * - أو إدخال المبلغ النهائي بالليرة السورية، وعندها يصبح سعر الصرف إلزاميًا.
     * - يمكن إدخال دفعة أولى اختيارية، وسيتم خصمها من المبلغ النهائي قبل توزيع الأقساط.
     *
     * 🔹 **أنماط إنشاء الأقساط:**
     * - **automatic**:
     *   يتم إنشاء الأقساط تلقائيًا بدءًا من تاريخ `installments_start_date`
     *   وحتى نهاية السنة الدراسية، مع توزيع المبلغ النهائي بالتساوي.
     *
     * - **manual**:
     *   يتم إنشاء الأقساط يدويًا بناءً على مصفوفة الأقساط المرسلة،
     *   ويجب أن يكون مجموع قيم الأقساط مساويًا للمبلغ النهائي بعد خصم أي دفعة أولى.
     * ",
     *     operationId="storeEnrollmentContract",
     *     tags={"EnrollmentContracts"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="بيانات عقد التسجيل ونظام الأقساط",
     *         @OA\JsonContent(
     *             required={"student_id","agreed_at","mode"},
     *
     *             @OA\Property(
     *                 property="student_id",
     *                 type="integer",
     *                 example=1,
     *                 description="معرّف الطالب"
     *             ),
     *
     *             @OA\Property(
     *                 property="total_amount_usd",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 nullable=true,
     *                 example=1000,
     *                 description="المبلغ الإجمالي قبل الخصم (اختياري)"
     *             ),
     *
     *             @OA\Property(
     *                 property="discount_percentage",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 maximum=100,
     *                 nullable=true,
     *                 example=10,
     *                 description="نسبة الخصم المطبقة على العقد (اختياري)"
     *             ),
     *
     *             @OA\Property(
     *                 property="discount_amount",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 nullable=true,
     *                 example=100,
     *                 description="قيمة الخصم الثابتة (اختياري)"
     *             ),
     *
     *             @OA\Property(
     *                 property="discount_reason",
     *                 type="string",
     *                 nullable=true,
     *                 example="خصم للطالب المتفوق",
     *                 description="سبب الخصم (اختياري)"
     *             ),
     *
     *             @OA\Property(
     *                 property="final_amount_usd",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 nullable=true,
     *                 example=900,
     *                 description="المبلغ النهائي بالدولار (يُستخدم عند العمل بالدولار)"
     *             ),
     *
     *             @OA\Property(
     *                 property="final_amount_syp",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 nullable=true,
     *                 example=11700000,
     *                 description="المبلغ النهائي بالليرة السورية"
     *             ),
     *
     *             @OA\Property(
     *                 property="exchange_rate_at_enrollment",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 nullable=true,
     *                 example=13000,
     *                 description="سعر الصرف عند التسجيل (إلزامي عند إدخال المبلغ بالليرة)"
     *             ),
     *
     *             @OA\Property(
     *                 property="agreed_at",
     *                 type="string",
     *                 format="date",
     *                 example="2025-11-02",
     *                 description="تاريخ الاتفاق"
     *             ),
     *
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 nullable=true,
     *                 example="يشمل الرسوم الإدارية والكتب"
     *             ),
     *
     *             @OA\Property(
     *                 property="is_active",
     *                 type="boolean",
     *                 nullable=true,
     *                 example=true,
     *                 description="حالة العقد"
     *             ),
     *
     *             @OA\Property(
     *                 property="mode",
     *                 type="string",
     *                 enum={"automatic","manual"},
     *                 example="automatic",
     *                 description="طريقة إنشاء الأقساط"
     *             ),
     *
     *             @OA\Property(
     *                 property="installments_start_date",
     *                 type="string",
     *                 format="date",
     *                 nullable=true,
     *                 example="2025-05-01",
     *                 description="تاريخ بدء الأقساط (مطلوب فقط عند اختيار automatic)"
     *             ),
     *
     *             @OA\Property(
     *                 property="installments_count",
     *                 type="integer",
     *                 minimum=1,
     *                 nullable=true,
     *                 example=8,
     *                 description="عدد الأقساط (مطلوب فقط عند اختيار manual)"
     *             ),
     *
     *             @OA\Property(
     *                 property="installments",
     *                 type="array",
     *                 nullable=true,
     *                 description="تفاصيل الأقساط (مطلوبة فقط عند اختيار manual)",
     *                 @OA\Items(
     *                     @OA\Property(property="installment_number", type="integer", example=1),
     *                     @OA\Property(property="due_date", type="string", format="date", example="2025-05-15"),
     *                     @OA\Property(property="planned_amount_usd", type="number", format="decimal", example=100)
     *                 )
     *             ),
     *
     *             @OA\Property(
     *                 property="first_payment",
     *                 type="object",
     *                 nullable=true,
     *                 description="الدفعة الأولى الاختيارية، سيتم خصمها من المبلغ النهائي قبل توزيع الأقساط",
     *                 @OA\Property(property="amount_usd", type="number", nullable=true, example=200),
     *                 @OA\Property(property="amount_syp", type="number", nullable=true, example=null),
     *                 @OA\Property(property="exchange_rate_at_payment", type="number", nullable=true, example=null),
     *                 @OA\Property(property="receipt_number", type="string", nullable=true, example="RCPT-001"),
     *                 @OA\Property(property="paid_date", type="string", format="date", nullable=true, example="2026-02-04"),
     *                 @OA\Property(property="description", type="string", nullable=true, example="دفعة أولى عند التسجيل"),
     *                 @OA\Property(property="institute_branch_id", type="integer", nullable=true, example=1)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء عقد التسجيل والأقساط بنجاح"
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من صحة البيانات"
     *     )
     * )
     */
    public function store(StoreEnrollmentContractRequest $request)
    {
        $data = $request->validated();

        // 1. تحديد is_active افتراضيًا
        $data['is_active'] = $data['is_active'] ?? true;

        // 2. التحقق من إدخال مبلغ
        if (empty($data['total_amount_usd']) && empty($data['final_amount_syp'])) {
            return $this->error('يجب إدخال المبلغ إما بالدولار أو بالليرة السورية.', 422);
        }

        // =========================
        // 3. الحسابات المالية (مزامنة الحسم: النسبة والمبلغ يعبران عن نفس القيمة)
        // =========================
        $baseUsd = !empty($data['total_amount_usd'])
            ? (float) $data['total_amount_usd']
            : (float) $data['final_amount_syp'] / (float) $data['exchange_rate_at_enrollment'];

        if (!empty($data['discount_amount']) && $data['discount_amount'] > 0) {
            // إذا أدخل المستخدم مبلغ الحسم، نحسب النسبة المئوية الموافقة له
            $discountAmountUsd = (float) $data['discount_amount'];
            $data['discount_percentage'] = ($baseUsd > 0) ? ($discountAmountUsd / $baseUsd) * 100 : 0;
        } else {
            // إذا أدخل النسبة فقط (أو لم يدخل شيء)، نحسب المبلغ بناءً على النسبة
            $discountPercentage = (float) ($data['discount_percentage'] ?? 0);
            $discountAmountUsd = ($baseUsd * $discountPercentage) / 100;
            $data['discount_amount'] = $discountAmountUsd;
        }

        $finalUsd = $baseUsd - $discountAmountUsd;

        // إعداد القيم للـ model (سيتم تشفيرها تلقائياً عند الحفظ)
        $data['total_amount_usd'] = $baseUsd;
        $data['final_amount_usd'] = $finalUsd;
        if (!empty($data['exchange_rate_at_enrollment'])) {
            $data['final_amount_syp'] = $finalUsd * $data['exchange_rate_at_enrollment'];
        }

        DB::beginTransaction();

        try {
            // =========================
            // 4. إنشاء عقد التسجيل
            // =========================
            $enrollmentContract = EnrollmentContract::create($data);

            // =========================
            // 5. معالجة الدفعة الأولى إذا وجدت
            // =========================
            $firstPaymentData = $request->input('first_payment', []);
            $hasFirstPayment = $firstPaymentData &&
                (
                    (!empty($firstPaymentData['amount_usd']) && $firstPaymentData['amount_usd'] > 0) ||
                    (!empty($firstPaymentData['amount_syp']) && $firstPaymentData['amount_syp'] > 0)
                );

            if ($hasFirstPayment) {
                $paymentRequest = new StorePaymentRequest();
                $validator = Validator::make($firstPaymentData, $paymentRequest->rules(), $paymentRequest->messages());

                if ($validator->fails()) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $validatedPayment = $validator->validated();
                $paymentService = app(PaymentService::class);
                $firstPayment = $paymentService->createFirstPayment($validatedPayment, $enrollmentContract);

                if ($firstPayment instanceof \Illuminate\Http\JsonResponse) {
                    DB::rollBack();
                    return $firstPayment;
                }

                $enrollmentContract->refresh();
                $enrollmentContract->first_payment_id = $firstPayment->id;
                $enrollmentContract->save();
            } else {
                $enrollmentContract->paid_amount_usd = 0;
                $enrollmentContract->save();
            }

            // =========================
            // 6. توزيع الأقساط
            // =========================
            if ($data['mode'] === 'automatic') {
                $startDate = $data['installments_start_date'];
                $startMonth = (int) date('m', strtotime($startDate));
                $installmentsCount = 12 - $startMonth + 1;

                if ($installmentsCount < 1) {
                    DB::rollBack();
                    return $this->error('تاريخ بدء الأقساط غير صالح.', 422);
                }

                $data['installments_count'] = $installmentsCount;
                $totalFinalUsd = $enrollmentContract->final_amount_usd;
                $rate = $enrollmentContract->exchange_rate_at_enrollment ?? null;

                // حساب القسط الأساسي وتقريبه لأقرب 10
                $baseAmountUsd = $totalFinalUsd / $installmentsCount;
                $roundedAmountUsd = round($baseAmountUsd / 10) * 10;

                $sumOtherInstallmentsUsd = 0;

                for ($i = 1; $i <= $installmentsCount; $i++) {
                    if ($i < $installmentsCount) {
                        $currentInstallmentUsd = $roundedAmountUsd;
                        $sumOtherInstallmentsUsd += $currentInstallmentUsd;
                    } else {
                        // القسط الأخير هو المتبقي لضمان دقة المبلغ الكلي
                        $currentInstallmentUsd = $totalFinalUsd - $sumOtherInstallmentsUsd;
                    }

                    PaymentInstallment::create([
                        'enrollment_contract_id' => $enrollmentContract->id,
                        'installment_number' => $i,
                        'due_date' => date('Y-m-d', strtotime($startDate . ' + ' . ($i - 1) . ' months')),
                        'planned_amount_usd' => $currentInstallmentUsd,
                        'exchange_rate_at_due_date' => $rate,
                        'planned_amount_syp' => $rate ? $currentInstallmentUsd * $rate : null,
                        'status' => 'pending',
                        'paid_amount_usd' => 0,
                    ]);
                }
            } elseif ($data['mode'] === 'manual') {
                $totalPlannedUsd = collect($data['installments'])->sum('planned_amount_usd');
                if (abs($totalPlannedUsd - $enrollmentContract->final_amount_usd) > 0.01) {
                    DB::rollBack();
                    return $this->error('مجموع الأقساط لا يساوي المبلغ النهائي.', 422);
                }

                $exchangeRate = $data['exchange_rate_at_enrollment'] ?? null;
                foreach ($data['installments'] as $inst) {
                    PaymentInstallment::create([
                        'enrollment_contract_id' => $enrollmentContract->id,
                        'installment_number' => $inst['installment_number'],
                        'due_date' => $inst['due_date'],
                        'planned_amount_usd' => $inst['planned_amount_usd'],
                        'exchange_rate_at_due_date' => $exchangeRate,
                        'planned_amount_syp' => $exchangeRate
                            ? $inst['planned_amount_usd'] * $exchangeRate
                            : null,
                        'status' => 'pending',
                        'paid_amount_usd' => 0,
                    ]);
                }
            } else {
                DB::rollBack();
                return $this->error('نمط إنشاء الأقساط غير صالح.', 422);
            }

            DB::commit();

            return $this->successResponse(
                new EnrollmentContractResource($enrollmentContract),
                'تم إنشاء عقد التسجيل والأقساط بنجاح',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('إستثناء أثناء إنشاء العقد: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->error('فشل في إنشاء العقد: ' . $e->getMessage(), 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/enrollment-contracts/preview",
     *     summary="معاينة وتحقق من عقد التسجيل والأقساط قبل الإنشاء",
     *     description="يعرض معاينة للأقساط في الوضع التلقائي (حساب عدد الأقساط تلقائيًا من تاريخ البدء حتى نهاية السنة الدراسية) أو يتحقق من صحة الأقساط في الوضع اليدوي (تطابق مجموع الأقساط مع المبلغ النهائي بالدولار وتطابق عدد الأقساط مع installments_count).",
     *     operationId="previewEnrollmentContract",
     *     tags={"EnrollmentContracts"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="بيانات العقد والأقساط للمعاينة والتحقق",
     *         @OA\JsonContent(
     *             required={"student_id", "agreed_at", "mode"},
     *             @OA\Property(property="student_id", type="integer", example=1, description="معرف الطالب"),
     *             @OA\Property(property="total_amount_usd", type="number", format="decimal", minimum=0, nullable=true, example=1000.00, description="المبلغ الكلي بالدولار قبل الخصم (اختياري إذا أُدخل final_amount_syp)"),
     *             @OA\Property(property="discount_percentage", type="number", format="decimal", minimum=0, maximum=100, nullable=true, example=10.00, description="نسبة الخصم (اختياري)"),
     *             @OA\Property(property="discount_amount", type="number", format="decimal", minimum=0, nullable=true, example=100.00, description="قيمة الخصم الثابتة (اختياري)"),
     *             @OA\Property(property="final_amount_usd", type="number", format="decimal", minimum=0, nullable=true, example=900.00, description="المبلغ النهائي بالدولار بعد الخصم (اختياري إذا أُدخل final_amount_syp)"),
     *             @OA\Property(property="final_amount_syp", type="number", format="decimal", minimum=0, nullable=true, example=11700000.00, description="المبلغ النهائي بالليرة السورية (اختياري إذا أُدخل final_amount_usd)"),
     *             @OA\Property(property="exchange_rate_at_enrollment", type="number", format="decimal", minimum=0, nullable=true, example=13000.0000, description="سعر الصرف عند التسجيل (مطلوب إذا أُدخل final_amount_syp)"),
     *             @OA\Property(property="agreed_at", type="string", format="date", example="2025-11-02", description="تاريخ الاتفاق"),
     *             @OA\Property(property="description", type="string", nullable=true, example="يشمل الكتب والرسوم الإدارية", description="وصف العقد (اختياري)"),
     *             @OA\Property(property="is_active", type="boolean", nullable=true, example=true, description="حالة تفعيل العقد (افتراضي: true)"),
     *             @OA\Property(property="mode", type="string", enum={"automatic","manual"}, example="automatic", description="نمط إنشاء الأقساط: automatic أو manual"),
     *             @OA\Property(property="installments_start_date", type="string", format="date", nullable=true, example="2025-05-01", description="تاريخ بدء الأقساط (مطلوب في الوضع التلقائي، يجب ≥ agreed_at)"),
     *             @OA\Property(property="installments_count", type="integer", minimum=1, nullable=true, example=8, description="عدد الأقساط (مطلوب في الوضع اليدوي)"),
     *             @OA\Property(
     *                 property="installments",
     *                 type="array",
     *                 nullable=true,
     *                 description="تفاصيل الأقساط (مطلوبة فقط في الوضع اليدوي)",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"installment_number", "due_date", "planned_amount_usd"},
     *                     @OA\Property(property="installment_number", type="integer", minimum=1, example=1, description="رقم القسط"),
     *                     @OA\Property(property="due_date", type="string", format="date", example="2025-05-15", description="تاريخ الاستحقاق"),
     *                     @OA\Property(property="planned_amount_usd", type="number", format="decimal", minimum=0, example=100.00, description="المبلغ المخطط بالدولار")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="first_payment",
     *                 type="object",
     *                 nullable=true,
     *                 description="الدفعة الأولى الاختيارية للمعاينة، يتم التحقق منها فقط دون إنشائها",
     *                 @OA\Property(property="amount_usd", type="number", nullable=true, example=200),
     *                 @OA\Property(property="amount_syp", type="number", nullable=true, example=null),
     *                 @OA\Property(property="exchange_rate_at_payment", type="number", nullable=true, example=null),
     *                 @OA\Property(property="receipt_number", type="string", nullable=true, example="RCPT-001"),
     *                 @OA\Property(property="paid_date", type="string", format="date", nullable=true, example="2026-02-04"),
     *                 @OA\Property(property="description", type="string", nullable=true, example="دفعة أولى عند التسجيل"),
     *                 @OA\Property(property="institute_branch_id", type="integer", nullable=true, example=1)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="معاينة أو تحقق ناجح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="installments_count", type="integer", example=8),
     *             @OA\Property(
     *                 property="installments",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="installment_number", type="integer", example=1),
     *                     @OA\Property(property="due_date", type="string", format="date", example="2025-05-01"),
     *                     @OA\Property(property="planned_amount_usd", type="number", format="decimal", example=112.50),
     *                     @OA\Property(property="exchange_rate_at_due_date", type="number", format="decimal", nullable=true, example=13000.0000),
     *                     @OA\Property(property="planned_amount_syp", type="number", format="decimal", nullable=true, example=1462500.00)
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="معاينة الأقساط في الوضع التلقائي")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="مجموع الأقساط لا يساوي المبلغ النهائي بالدولار."),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function preview(StoreEnrollmentContractRequest $request)
    {
        $data = $request->validated();

        // تعيين is_active افتراضيًا
        $data['is_active'] = $data['is_active'] ?? true;

        // التحقق من إدخال مبلغ
        if (empty($data['total_amount_usd']) && empty($data['final_amount_syp'])) {
            return $this->error('يجب إدخال المبلغ إما بالدولار أو بالليرة السورية.', 422);
        }

        // =========================
        // الحسابات المالية (مزامنة الحسم: النسبة والمبلغ يعبران عن نفس القيمة)
        // =========================
        $baseUsd = !empty($data['total_amount_usd'])
            ? (float) $data['total_amount_usd']
            : (float) $data['final_amount_syp'] / (float) $data['exchange_rate_at_enrollment'];

        if (!empty($data['discount_amount']) && $data['discount_amount'] > 0) {
            $discountAmountUsd = (float) $data['discount_amount'];
            $data['discount_percentage'] = ($baseUsd > 0) ? ($discountAmountUsd / $baseUsd) * 100 : 0;
        } else {
            $discountPercentage = (float) ($data['discount_percentage'] ?? 0);
            $discountAmountUsd = ($baseUsd * $discountPercentage) / 100;
            $data['discount_amount'] = $discountAmountUsd;
        }

        $finalUsd = $baseUsd - $discountAmountUsd;

        $data['total_amount_usd'] = $baseUsd;
        $data['final_amount_usd'] = $finalUsd;

        $exchangeRate = $data['exchange_rate_at_enrollment'] ?? null;

        if ($exchangeRate) {
            $data['final_amount_syp'] = $finalUsd * $exchangeRate;
        }

        // =========================
        // التحقق من الدفعة الأولى
        // =========================
        $firstPaymentData = $request->input('first_payment', null);
        $firstPaymentPreview = null;

        if ($firstPaymentData) {
            // تحقق من مبلغ الدفعة الأولى بالدولار
            $firstUsd = !empty($firstPaymentData['amount_usd'])
                ? $firstPaymentData['amount_usd']
                : (!empty($firstPaymentData['amount_syp']) && $exchangeRate
                    ? $firstPaymentData['amount_syp'] / $exchangeRate
                    : 0);

            if ($firstUsd < 0) {
                return $this->error('مبلغ الدفعة الأولى غير صالح.', 422);
            }

            if ($firstUsd > $finalUsd) {
                return $this->error('الدفعة الأولى أكبر من المبلغ النهائي.', 422);
            }

            // =========================
            // تحقق من الحقول المطلوبة باستخدام StorePaymentRequest rules
            // =========================
            $validator = Validator::make(
                $firstPaymentData,
                (new \Modules\Payments\Http\Requests\StorePaymentRequest())->rules(),
                (new \Modules\Payments\Http\Requests\StorePaymentRequest())->messages()
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $firstPaymentPreview = [
                'amount_usd' => round($firstUsd, 2),
                'amount_syp' => $exchangeRate ? round($firstUsd * $exchangeRate, 2) : null,
                'currency' => !empty($firstPaymentData['amount_usd']) ? 'USD' : 'SYP',
                'receipt_number' => $firstPaymentData['receipt_number'] ?? null,
                'paid_date' => $firstPaymentData['paid_date'] ?? null,
                'description' => $firstPaymentData['description'] ?? null,
                'institute_branch_id' => $firstPaymentData['institute_branch_id'] ?? null,
            ];

            // خصم الدفعة الأولى من المبلغ النهائي فقط للمعاينة
            $finalUsd -= $firstUsd;
        }

        // =========================
        // إعداد الأقساط
        // =========================
        $installments = [];
        $installmentsCount = 0;

        if ($data['mode'] === 'automatic') {
            $startDate = $data['installments_start_date'];
            $startMonth = (int) date('m', strtotime($startDate));
            $installmentsCount = 12 - $startMonth + 1;

            if ($installmentsCount < 1) {
                return $this->error('تاريخ بدء الأقساط غير صالح.', 422);
            }

            // حساب القسط الأساسي وتقريبه لأقرب 10 مع الحفاظ على المجموع
            $baseAmountUsd = $finalUsd / $installmentsCount;
            $roundedAmountUsd = round($baseAmountUsd / 10) * 10;
            $sumOtherInstallmentsUsd = 0;

            for ($i = 1; $i <= $installmentsCount; $i++) {
                $dueDate = date('Y-m-d', strtotime($startDate . ' + ' . ($i - 1) . ' months'));

                if ($i < $installmentsCount) {
                    $currentInstallmentUsd = $roundedAmountUsd;
                    $sumOtherInstallmentsUsd += $currentInstallmentUsd;
                } else {
                    $currentInstallmentUsd = $finalUsd - $sumOtherInstallmentsUsd;
                }

                $installments[] = [
                    'installment_number' => $i,
                    'due_date' => $dueDate,
                    'planned_amount_usd' => round($currentInstallmentUsd, 2),
                    'exchange_rate_at_due_date' => $exchangeRate,
                    'planned_amount_syp' => $exchangeRate
                        ? round($currentInstallmentUsd * $exchangeRate, 2)
                        : null,
                ];
            }
        } elseif ($data['mode'] === 'manual') {
            $installments = $data['installments'] ?? [];
            $installmentsCount = $data['installments_count'] ?? count($installments);

            if (count($installments) !== (int) $installmentsCount) {
                return $this->error('عدد الأقساط المدخلة لا يتطابق مع installments_count.', 422);
            }

            $totalPlannedUsd = collect($installments)->sum('planned_amount_usd');

            if (abs($totalPlannedUsd - $finalUsd) > 0.01) {
                return $this->error(
                    'مجموع الأقساط (' . number_format($totalPlannedUsd, 2) .
                    ') لا يساوي المبلغ النهائي بعد خصم الدفعة الأولى (' . number_format($finalUsd, 2) . ').',
                    422
                );
            }

            foreach ($installments as &$inst) {
                $inst['exchange_rate_at_due_date'] = $exchangeRate;
                $inst['planned_amount_syp'] = $exchangeRate
                    ? round($inst['planned_amount_usd'] * $exchangeRate, 2)
                    : null;
            }
        } else {
            return $this->error('نمط إنشاء الأقساط غير صالح.', 422);
        }

        return $this->successResponse([
            'first_payment' => $firstPaymentPreview,
            'installments_count' => $installmentsCount,
            'installments' => $installments,
            'message' => 'معاينة العقد والأقساط مع الدفعة الأولى'
        ]);
    }



    /**
     * @OA\Get(
     *     path="/api/enrollment-contracts/{id}",
     *     summary="عرض تفاصيل عقد تسجيل محدد",
     *     tags={"EnrollmentContracts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف عقد التسجيل",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات عقد التسجيل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات عقد التسجيل بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="student_id", type="integer", example=1),
     *                 @OA\Property(property="total_amount_usd", type="number", format="float", example=1000.00),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=10.00),
     *                 @OA\Property(property="discount_amount", type="number", format="float", example=100.00),
     *                 @OA\Property(property="final_amount_usd", type="number", format="float", example=900.00),
     *                 @OA\Property(property="exchange_rate_at_enrollment", type="number", format="float", example=15000.0000),
     *                 @OA\Property(property="final_amount_syp", type="number", format="float", example=13500000.00),
     *                 @OA\Property(property="agreed_at", type="string", format="date", example="2023-01-15"),
     *                 @OA\Property(property="description", type="string", example="يشمل الكتب والرسوم الإدارية"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="عقد التسجيل غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="عقد التسجيل غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $enrollmentContract = EnrollmentContract::find($id);

        if (!$enrollmentContract) {
            return $this->error('عقد التسجيل غير موجود', 404);
        }

        return $this->successResponse(
            new EnrollmentContractResource($enrollmentContract),
            'تم جلب بيانات عقد التسجيل بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/enrollment-contracts/{id}",
     *     summary="تحديث بيانات عقد تسجيل",
     *     tags={"EnrollmentContracts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف عقد التسجيل",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="student_id", type="integer", example=2),
     *             @OA\Property(property="total_amount_usd", type="number", format="float", example=1200.00),
     *             @OA\Property(property="discount_percentage", type="number", format="float", example=15.00),
     *             @OA\Property(property="discount_amount", type="number", format="float", example=180.00),
     *             @OA\Property(property="discount_reason", type="string", nullable=true, example="خصم للطالب المتفوق"),
     *             @OA\Property(property="final_amount_usd", type="number", format="float", example=1020.00),
     *             @OA\Property(property="exchange_rate_at_enrollment", type="number", format="float", example=16000.0000),
     *             @OA\Property(property="final_amount_syp", type="number", format="float", example=16320000.00),
     *             @OA\Property(property="agreed_at", type="string", format="date", example="2023-02-15"),
     *             @OA\Property(property="description", type="string", example="يشمل الكتب والرسوم الإدارية مع خصم خاص"),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات عقد التسجيل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات عقد التسجيل بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="student_id", type="integer", example=2),
     *                 @OA\Property(property="total_amount_usd", type="number", format="float", example=1200.00),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=15.00),
     *                 @OA\Property(property="discount_amount", type="number", format="float", example=180.00),
     *                 @OA\Property(property="discount_reason", type="string", nullable=true, example="خصم للطالب المتفوق"),
     *                 @OA\Property(property="final_amount_usd", type="number", format="float", example=1020.00),
     *                 @OA\Property(property="exchange_rate_at_enrollment", type="number", format="float", example=16000.0000),
     *                 @OA\Property(property="final_amount_syp", type="number", format="float", example=16320000.00),
     *                 @OA\Property(property="agreed_at", type="string", format="date", example="2023-02-15"),
     *                 @OA\Property(property="description", type="string", example="يشمل الكتب والرسوم الإدارية مع خصم خاص"),
     *                 @OA\Property(property="is_active", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="عقد التسجيل غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="عقد التسجيل غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateEnrollmentContractRequest $request, $id)
    {
        $enrollmentContract = EnrollmentContract::find($id);

        if (!$enrollmentContract) {
            return $this->error('عقد التسجيل غير موجود', 404);
        }

        $data = $request->validated();

        // إعادة حساب المبالغ المالية إذا تغير أي حقل ذو صلة
        if (
            array_key_exists('total_amount_usd', $data) ||
            array_key_exists('discount_percentage', $data) ||
            array_key_exists('discount_amount', $data) ||
            array_key_exists('exchange_rate_at_enrollment', $data)
        ) {
            $baseUsd = array_key_exists('total_amount_usd', $data) ? (float) $data['total_amount_usd'] : (float) $enrollmentContract->total_amount_usd;
            $rate = array_key_exists('exchange_rate_at_enrollment', $data) ? (float) $data['exchange_rate_at_enrollment'] : (float) $enrollmentContract->exchange_rate_at_enrollment;

            // تحديد قيمة الحسم بناءً على ما تم إرساله (الأولوية للمبلغ إذا أرسل كلاهما للتحديث)
            if (array_key_exists('discount_amount', $data) && $data['discount_amount'] > 0) {
                $discountAmountUsd = (float) $data['discount_amount'];
                $data['discount_percentage'] = ($baseUsd > 0) ? ($discountAmountUsd / $baseUsd) * 100 : 0;
            } elseif (array_key_exists('discount_percentage', $data)) {
                $discountPercentage = (float) $data['discount_percentage'];
                $discountAmountUsd = ($baseUsd * $discountPercentage) / 100;
                $data['discount_amount'] = $discountAmountUsd;
            } else {
                // لم يتم إرسال أي حسم جديد، نستخدم المسجل حالياً
                $discountAmountUsd = (float) $enrollmentContract->discount_amount;
            }

            $finalUsd = $baseUsd - $discountAmountUsd;
            $data['final_amount_usd'] = $finalUsd;

            if ($rate > 0) {
                $data['final_amount_syp'] = $finalUsd * $rate;
            }
        }

        $enrollmentContract->update($data);

        return $this->successResponse(
            new EnrollmentContractResource($enrollmentContract->refresh()),
            'تم تحديث بيانات عقد التسجيل بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/enrollment-contracts/{id}",
     *     summary="حذف عقد تسجيل",
     *     tags={"EnrollmentContracts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف عقد التسجيل",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف عقد التسجيل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف عقد التسجيل بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="عقد التسجيل غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="عقد التسجيل غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $enrollmentContract = EnrollmentContract::find($id);

        if (!$enrollmentContract) {
            return $this->error('عقد التسجيل غير موجود', 404);
        }

        // التحقق من وجود دفعات مرتبطة بالعقد
        if ($enrollmentContract->payments()->exists()) {
            return $this->error(
                'لا يمكن حذف عقد التسجيل لوجود دفعات مالية مرتبطة به',
                409
            );
        }

        $enrollmentContract->delete();

        return $this->successResponse(
            null,
            'تم حذف عقد التسجيل بنجاح',
            200
        );
    }

}