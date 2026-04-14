<?php

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\InstallmentAdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Payments\Models\Payment;
use Modules\EnrollmentContracts\Models\EnrollmentContract;
use Modules\PaymentInstallments\Models\PaymentInstallment;
use Modules\PaymentEditRequests\Models\PaymentEditRequest;
use Modules\Payments\Http\Requests\StorePaymentRequest;
use Modules\Payments\Http\Requests\UpdatePaymentRequest;
use Modules\Payments\Http\Resources\PaymentResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Illuminate\Support\Facades\DB;
use Modules\Payments\Http\Resources\PaymentEditRequestResource;
use Modules\Students\Models\Student;

class PaymentsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/payments",
     *     summary="قائمة جميع المدفوعات",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع المدفوعات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع المدفوعات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="receipt_number", type="string", example="REC-001"),
     *                     @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                     @OA\Property(property="enrollment_contract_id", type="integer", example=1),
     *                     @OA\Property(property="payment_installments_id", type="integer", example=1),
     *                     @OA\Property(property="amount_usd", type="number", format="float", example=100.00),
     *                     @OA\Property(property="amount_syp", type="number", format="float", example=1000000.00),
     *                     @OA\Property(property="exchange_rate_at_payment", type="number", format="float", example=10000.0000),
     *                     @OA\Property(property="currency", type="string", example="USD"),
     *                     @OA\Property(property="due_date", type="string", format="date", example="2023-01-01"),
     *                     @OA\Property(property="paid_date", type="string", format="date", example="2023-01-02"),
     *                     @OA\Property(property="description", type="string", example="دفعة نقدًا"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد مدفوعات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي مدفوعة مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $payments = Payment::all();
        if ($payments->isEmpty()) {
            return $this->error('لا يوجد أي مدفوعة مسجلة حالياً', 404);
        }

        return $this->successResponse(
            PaymentResource::collection($payments),
            'تم جلب جميع المدفوعات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/payments",
     *     summary="إضافة مدفوعة جديدة وتطبيقها تلقائياً على الأقساط المعلقة",
     *     description="يقوم هذا الإندبوينت بإنشاء دفعة جديدة وتوزيع المبلغ المدفوع على الأقساط المعلقة (pending) للعقد المرتبط بالطالب المحدد. يتم جلب العقد تلقائياً (النشط أو الأحدث). يتم تحويل المبلغ إلى USD داخلياً للتطبيق، وإذا كانت العملة USD يُستخدم amount_usd مباشرة، أما إذا SYP فيتم التحويل باستخدام exchange_rate_at_payment. إذا غطى المبلغ قسطاً كاملاً يصبح status='paid' و paid_amount_usd = planned_amount_usd، وإذا جزئياً (للقسط الأخير فقط) يزيد paid_amount_usd بالمبلغ المتبقي ويبقى status='pending' ويتم ربط الدفعة بالقسط. إذا تجاوز المبلغ الإجمالي المعلق يتم رفض الطلب. لا يتم استخدام enrollment_contract_id في الإدخال (يتم جلبُه تلقائياً من الطالب).",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"receipt_number","institute_branch_id","student_id","exchange_rate_at_payment","currency"},
     *             @OA\Property(property="receipt_number", type="string", example="REC-001", description="رقم الإيصال الفريد"),
     *             @OA\Property(property="institute_branch_id", type="integer", example=1, description="معرف فرع المعهد"),
     *             @OA\Property(property="student_id", type="integer", example=1, description="معرف الطالب (مطلوب، يُستخدم لجلب العقد تلقائياً)"),
     *             @OA\Property(property="amount_usd", type="number", format="float", example=100.00, description="المبلغ بالدولار (مطلوب إذا كانت العملة USD)"),
     *             @OA\Property(property="amount_syp", type="number", format="float", example=1000000.00, description="المبلغ بالليرة السورية (مطلوب إذا كانت العملة SYP)"),
     *             @OA\Property(property="exchange_rate_at_payment", type="number", format="float", example=10000.0000, description="سعر الصرف لحظة الدفع (مطلوب للتحويل إذا كانت العملة SYP)"),
     *             @OA\Property(property="currency", type="string", enum={"USD", "SYP"}, example="USD", description="العملة المدفوعة (مطلوب)"),
     *             @OA\Property(property="paid_date", type="string", format="date", example="2023-01-02", description="تاريخ الدفع الفعلي (اختياري، افتراضياً تاريخ اليوم)"),
     *             @OA\Property(property="description", type="string", example="دفعة نقدًا", description="وصف الدفعة (اختياري)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء المدفوعة وتطبيقها على الأقساط بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء المدفوعة وتطبيقها على الأقساط بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="receipt_number", type="string", example="REC-001"),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                 @OA\Property(property="enrollment_contract_id", type="integer", example=1, description="معرف العقد الذي تم جلبُه تلقائياً من الطالب"),
     *                 @OA\Property(property="student_id", type="integer", example=1, description="معرف الطالب"),
     *                 @OA\Property(property="payment_installments_id", type="integer", example=null, description="معرف القسط المرتبط إذا كان الدفع جزئياً، خلاف ذلك null"),
     *                 @OA\Property(property="amount_usd", type="number", format="float", example=100.00, description="المبلغ المحول إلى USD (الذي تم تطبيقه على الأقساط)"),
     *                 @OA\Property(property="amount_syp", type="number", format="float", example=1000000.00),
     *                 @OA\Property(property="exchange_rate_at_payment", type="number", format="float", example=10000.0000),
     *                 @OA\Property(property="currency", type="string", example="USD"),
     *                 @OA\Property(property="paid_date", type="string", format="date", example="2023-01-02"),
     *                 @OA\Property(property="description", type="string", example="دفعة نقدًا", description="وصف الدفعة"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="خطأ في الطلب",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا توجد أقساط معلقة لهذا العقد"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب أو العقد غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد عقد تسجيل نشط أو مرتبط بالطالب المحدد"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object", example={"student_id": {"معرف الطالب مطلوب"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء معالجة الدفعة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function store(StorePaymentRequest $request)
    {
        DB::beginTransaction();

        try {
            // حساب المبلغ بالـ USD المراد دفعه (بناءً على العملة)
            $amountUsd = $request->currency === 'USD'
                ? $request->amount_usd
                : ($request->amount_syp / $request->exchange_rate_at_payment);

            // 1. جلب الطالب أولاً
            $student = Student::find($request->student_id);
            if (!$student) {
                DB::rollBack();
                return $this->error('الطالب المحدد غير موجود', 404);
            }

            // 2. جلب العقد المرتبط بالطالب (النشط أولاً، أو الأحدث إذا ما كان)
            $contract = EnrollmentContract::where('student_id', $student->id)
                ->where('is_active', true)
                ->first();

            if (!$contract) {
                $contract = EnrollmentContract::where('student_id', $student->id)
                    ->latest('created_at')
                    ->first();
            }

            if (!$contract) {
                DB::rollBack();
                return $this->error('لا يوجد عقد تسجيل نشط أو مرتبط بالطالب المحدد', 404);
            }

            // 3. الحصول على الأقساط غير المدفوعة بالكامل
            $pendingInstallments = PaymentInstallment::where('enrollment_contract_id', $contract->id)
                ->where('status', 'pending')
                ->orderBy('installment_number')
                ->get();

            if ($pendingInstallments->isEmpty()) {
                DB::rollBack();
                return $this->error('لا توجد أقساط معلقة لهذا العقد', 400);
            }

            $remainingAmount = $amountUsd;
            $lastUpdatedInstallmentId = null;

            foreach ($pendingInstallments as $installment) {
                if ($installment->status === 'paid') {
                    continue;
                }

                $dueAmount = $installment->planned_amount_usd - $installment->paid_amount_usd;
                if ($remainingAmount >= $dueAmount) {
                    $installment->paid_amount_usd += $dueAmount;
                    $remainingAmount -= $dueAmount;
                } else {
                    $installment->paid_amount_usd += $remainingAmount;
                    $lastUpdatedInstallmentId = $installment->id;
                    $remainingAmount = 0;
                }

                $installment->status = ($installment->paid_amount_usd >= $installment->planned_amount_usd) ? 'paid' : 'pending';
                $installment->save();

                if ($remainingAmount <= 0) break;
            }

            if ($remainingAmount > 0) {
                DB::rollBack();
                return $this->error('المبلغ المدفوع يتجاوز إجمالي الأقساط المعلقة', 400);
            }

            // إنشاء الدفعة الرئيسية
            $paymentData = $request->validated();
            $paymentData['enrollment_contract_id'] = $contract->id;
            $paymentData['amount_usd'] = $amountUsd;
            $paymentData['payment_installments_id'] = $lastUpdatedInstallmentId;
            $payment = Payment::create($paymentData);

            // تحديث إجمالي المدفوع في العقد
            $contract->paid_amount_usd += $amountUsd;
            $contract->save();

            DB::commit();

            return $this->successResponse(
                new PaymentResource($payment),
                'تم إنشاء المدفوعة وتطبيقها على الأقساط بنجاح',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return $this->error('حدث خطأ أثناء معالجة الدفعة', 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     summary="عرض تفاصيل مدفوعة محددة",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المدفوعة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات المدفوعة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات المدفوعة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="receipt_number", type="string", example="REC-001"),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                 @OA\Property(property="enrollment_contract_id", type="integer", example=1),
     *                 @OA\Property(property="payment_installments_id", type="integer", example=1),
     *                 @OA\Property(property="amount_usd", type="number", format="float", example=100.00),
     *                 @OA\Property(property="amount_syp", type="number", format="float", example=1000000.00),
     *                 @OA\Property(property="exchange_rate_at_payment", type="number", format="float", example=10000.0000),
     *                 @OA\Property(property="currency", type="string", example="USD"),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2023-01-01"),
     *                 @OA\Property(property="paid_date", type="string", format="date", example="2023-01-02"),
     *                 @OA\Property(property="description", type="string", example="دفعة نقدًا"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المدفوعة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المدفوعة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->error('المدفوعة غير موجودة', 404);
        }

        return $this->successResponse(
            new PaymentResource($payment),
            'تم جلب بيانات المدفوعة بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/payments/{id}",
     *     summary="تحديث بيانات مدفوعة مع إعادة تطبيق الفرق على الأقساط",
     *     description="يقوم هذا الإندبوينت بتحديث بيانات الدفعة وإعادة توزيع الفرق في المبلغ (amount_usd الجديد مقارنة بالقديم) على الأقساط. 
     * إذا زاد المبلغ (مثال: من 100 إلى 1000 USD)، يُطبق الفرق (+900) على الأقساط المعلقة التالية بالترتيب (يزيد paid_amount_usd، ويحدث status إلى 'paid' إذا أصبح كاملاً). 
     * إذا نقص المبلغ (مثال: من 2600 إلى 100 USD)، يُعاد الفرق (-2500) من الأقساط المدفوعة الأخيرة (ينقص paid_amount_usd، ويحدث status إلى 'pending' إذا أصبح جزئياً). 
     * يتم التحويل إلى USD داخلياً إذا لزم. إذا تجاوز الفرق الإجمالي المتاح يتم رفض الطلب.",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المدفوعة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="receipt_number", type="string", example="REC-002"),
     *             @OA\Property(property="institute_branch_id", type="integer", example=2),
     *             @OA\Property(property="enrollment_contract_id", type="integer", example=2),
     *             @OA\Property(property="amount_usd", type="number", format="float", example=200.00),
     *             @OA\Property(property="amount_syp", type="number", format="float", example=2000000.00),
     *             @OA\Property(property="exchange_rate_at_payment", type="number", format="float", example=10000.0000),
     *             @OA\Property(property="currency", type="string", example="SYP"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2023-02-01"),
     *             @OA\Property(property="paid_date", type="string", format="date", example="2023-02-02"),
     *             @OA\Property(property="description", type="string", example="حوالة بنكية"),
     *             @OA\Property(property="reason", type="string", example="تسريع معالجة الدفعة", description="سبب تعديل أو تعجيل الدفعة")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات المدفوعة وإعادة تطبيق الفرق بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات المدفوعة وإعادة تطبيق الفرق بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="receipt_number", type="string", example="REC-002"),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=2),
     *                 @OA\Property(property="enrollment_contract_id", type="integer", example=2),
     *                 @OA\Property(property="amount_usd", type="number", format="float", example=200.00),
     *                 @OA\Property(property="amount_syp", type="number", format="float", example=2000000.00),
     *                 @OA\Property(property="exchange_rate_at_payment", type="number", format="float", example=10000.0000),
     *                 @OA\Property(property="currency", type="string", example="SYP"),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2023-02-01"),
     *                 @OA\Property(property="paid_date", type="string", format="date", example="2023-02-02"),
     *                 @OA\Property(property="description", type="string", example="حوالة بنكية"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="خطأ في الطلب (مثل تجاوز الفرق المتاح)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الفرق في المبلغ يتجاوز الأقساط المتاحة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المدفوعة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المدفوعة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */

    public function update(UpdatePaymentRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $payment = Payment::find($id);

            if (!$payment) {
                DB::rollBack();
                return $this->error('المدفوعة غير موجودة', 404);
            }

            $user = Auth::user();
            $validatedData = $request->validated();

            /** @var \Modules\Users\Models\User|\Spatie\Permission\Traits\HasRoles $user */
            if ($user->hasRole('admin')) {
                // حساب الفرق بالمبلغ (لأجل الأقساط فقط)
                $oldAmountUsd = $payment->amount_usd;
                $newAmountUsd = $validatedData['amount_usd'] ?? $oldAmountUsd;
                $difference = $newAmountUsd - $oldAmountUsd;

                // تحديث الدفعة
                $payment->update($validatedData);

                // تعديل الأقساط بناءً على الفرق بالمبلغ باستخدام Service
                if (abs($difference) >= 0.01) {
                    $installmentService = new InstallmentAdjustmentService();
                    $installmentService->adjustForAmountDifference($payment, $difference);
                }

                DB::commit();

                return $this->successResponse(
                    new PaymentResource($payment),
                    'تم تحديث الدفعة بنجاح',
                    200
                );
            }

            // 🔹 إذا المستخدم ليس admin → إنشاء طلب تعديل داخلي (reason محدد داخلياً)
            $editRequest = PaymentEditRequest::create([
                'payment_id' => $payment->id,
                'requester_id' => $user->id,
                'original_data' => $payment->toArray(),
                'proposed_changes' => $validatedData,
                'reason' => $request->reason ?? null, // يُحدد داخلياً عند تقديم الطلب
                'status' => 'pending',
                'action' => 'update',
            ]);

            // إرسال إشعار للمدراء
            $admins = \Modules\Users\Models\User::role('admin')->get();
            $tokens = [];
            foreach ($admins as $admin) {
                $tokens = array_merge($tokens, $admin->fcmTokens->pluck('token')->toArray());
            }

            if (!empty($tokens)) {
                $title = 'طلب تعديل دفعة';
                $body = "تم طلب تعديل الدفعة رقم #{$payment->id}. الرجاء المراجعة والموافقة أو الرفض.";

                app(\App\Services\FirebaseService::class)
                    ->sendToMultipleTokens($tokens, $title, $body, [
                        'edit_request_id' => $editRequest->id,
                        'payment_id' => $payment->id,
                        'action' => 'payment_edit_request'
                    ]);
            }

            DB::commit();

            return $this->successResponse(
                new PaymentEditRequestResource($editRequest),
                'تم إرسال طلب التعديل وينتظر موافقة المدير',
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('حدث خطأ أثناء تحديث الدفعة', 500);
        }
    }




    /**
     * @OA\Delete(
     *     path="/api/payments/{id}",
     *     summary="حذف مدفوعة أو إرسال طلب حذف",
     *     description="
    يقوم هذا المسار بحذف مدفوعة أو إنشاء طلب حذف حسب صلاحيات المستخدم.

    🧠 **السلوك:**
    - إذا كان المستخدم **admin**:
    - يتم حذف المدفوعة مباشرة.
    - يتم عكس تأثير المدفوعة على الأقساط تلقائياً عبر `InstallmentAdjustmentService`.
    - إذا كان المستخدم **غير admin**:
    - لا يتم حذف المدفوعة مباشرة.
    - يتم إنشاء **طلب حذف مدفوعة** (`payment_edit_request`) بحالة `pending`.
    - يتم إرسال إشعار للمدراء للموافقة أو الرفض.
    ",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المدفوعة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="reason",
     *                 type="string",
     *                 example="تم إدخال المدفوعة بالخطأ",
     *                 description="سبب طلب حذف المدفوعة (يُستخدم فقط إذا كان المستخدم غير admin)"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف المدفوعة مباشرة (admin) أو تم إرسال طلب حذف",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="تم حذف المدفوعة بنجاح أو تم إرسال طلب حذف الدفعة وينتظر موافقة المدير"
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="المدفوعة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المدفوعة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء طلب حذف الدفعة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $payment = Payment::find($id);

            if (!$payment) {
                DB::rollBack();
                return $this->error('المدفوعة غير موجودة', 404);
            }

            $user = Auth::user();

            /** @var \Modules\Users\Models\User|\Spatie\Permission\Traits\HasRoles $user */
            if ($user->hasRole('admin')) {

                // عكس تأثير الدفعة على الأقساط
                $installmentService = new InstallmentAdjustmentService();
                $installmentService->adjustForAmountDifference(
                    $payment,
                    $payment->amount_usd * -1
                );

                $payment->delete();

                DB::commit();

                return $this->successResponse(
                    null,
                    'تم حذف المدفوعة بنجاح',
                    200
                );
            }

            // 🔹 إذا المستخدم ليس admin → إنشاء طلب حذف دفعة
            $editRequest = PaymentEditRequest::create([
                'payment_id'       => $payment->id,
                'requester_id'     => $user->id,
                'original_data'    => $payment->toArray(),
                'proposed_changes' => null,
                'reason'           => request('reason') ?? null,
                'status'           => 'pending',
                'action'           => 'delete',
            ]);

            // إرسال إشعار للمدراء
            $admins = \Modules\Users\Models\User::role('admin')->get();
            $tokens = [];

            foreach ($admins as $admin) {
                if ($admin->fcmTokens) {
                    $tokens = array_merge(
                        $tokens,
                        $admin->fcmTokens->pluck('token')->toArray()
                    );
                }
            }

            if (!empty($tokens)) {
                $title = 'طلب حذف دفعة';
                $body  = "تم طلب حذف الدفعة رقم #{$payment->id}. الرجاء المراجعة.";

                app(\App\Services\FirebaseService::class)
                    ->sendToMultipleTokens($tokens, $title, $body, [
                        'edit_request_id' => $editRequest->id,
                        'payment_id'      => $payment->id,
                        'action'          => 'payment_delete_request',
                    ]);
            }

            DB::commit();

            return $this->successResponse(
                new PaymentEditRequestResource($editRequest),
                'تم إرسال طلب حذف الدفعة وينتظر موافقة المدير',
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error(
                'حدث خطأ أثناء طلب حذف الدفعة: ' . $e->getMessage(),
                500
            );
        }
    }


    /**
     * @OA\Get(
     *     path="/api/payments/{payment_id}/edit-requests",
     *     summary="Get all edit requests for a specific payment",
     *     description="Fetches all pending or processed edit requests associated with a specific payment, including payment details.",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="payment_id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment to fetch edit requests for",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Edit requests retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Edit requests fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="payment_id", type="integer", example=1),
     *                     @OA\Property(property="requester_id", type="integer", example=5),
     *                     @OA\Property(property="original_data", type="object"),
     *                     @OA\Property(property="proposed_changes", type="object"),
     *                     @OA\Property(property="reason", type="string", example="Correct wrong amount"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-16T10:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-16T11:00:00.000000Z"),
     *                     @OA\Property(property="payment", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Payment not found"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function getEditRequestsByPayment($payment_id)
    {
        try {
            $payment = Payment::find($payment_id);

            if (!$payment) {
                return $this->error('الدفعة غير موجودة', 404);
            }

            // جلب كل طلبات التعديل المرتبطة بالدفعة
            $editRequests = PaymentEditRequest::where('payment_id', $payment->id)
                ->with('payment') // تجيب الدفعة المرتبطة
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse(
                $editRequests,
                'تم جلب طلبات تعديل الدفعة بنجاح',
                200
            );
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء جلب طلبات تعديل الدفعة', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/payments/edit-requests/{id}/approve",
     *     summary="الموافقة على طلب تعديل أو حذف دفعة",
     *     description="
    يقوم هذا المسار بالموافقة على طلب تعديل دفعة مالية أو طلب حذفها حسب نوع الطلب.

    🧠 **السلوك:**
    - إذا كان نوع الطلب `action = update`:
    - يتم تطبيق التعديلات المقترحة على الدفعة.
    - يتم حساب الفرق في المبلغ وتعديل الأقساط عبر `InstallmentAdjustmentService`.
    - إذا كان نوع الطلب `action = delete`:
    - يتم عكس تأثير الدفعة على الأقساط.
    - يتم حذف الدفعة.

    📨 **الإشعارات:**
    - يتم إرسال إشعار إلى:
    - مقدم الطلب
    - الطالب
    - ولي الأمر

    🗑️ بعد التنفيذ:
    - يتم حذف طلب التعديل من جدول `payment_edit_requests`.
    ",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف طلب التعديل",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تمت الموافقة على الطلب وتنفيذ الإجراء بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="تمت الموافقة على الطلب وتنفيذ الإجراء بنجاح"
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="طلب التعديل تمت معالجته مسبقاً",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="طلب التعديل تمت معالجته مسبقاً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="طلب التعديل أو الدفعة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="طلب التعديل غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء الموافقة على الطلب"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function approveEditRequest($id)
    {
        DB::beginTransaction();

        try {
            $editRequest = PaymentEditRequest::with(
                'payment.enrollmentContract.student.user.fcmTokens',
                'payment.enrollmentContract.student.family.user.fcmTokens',
                'requester.fcmTokens'
            )->find($id);

            if (!$editRequest) {
                return $this->error('طلب التعديل غير موجود', 404);
            }

            if ($editRequest->status !== 'pending') {
                return $this->error('طلب التعديل تمت معالجته مسبقاً', 400);
            }

            $payment = $editRequest->payment;

            if (!$payment) {
                return $this->error('الدفعة المرتبطة غير موجودة', 404);
            }

            // ============================
            // تنفيذ الإجراء حسب نوع الطلب
            // ============================

            if ($editRequest->action === 'update') {

                $proposedChanges = $editRequest->proposed_changes ?? [];
                $oldAmountUsd = $payment->amount_usd;
                $newAmountUsd = $proposedChanges['amount_usd'] ?? $oldAmountUsd;
                $difference   = $newAmountUsd - $oldAmountUsd;

                if (!empty($editRequest->reason)) {
                    $proposedChanges['reason'] = $editRequest->reason;
                }

                $payment->update($proposedChanges);

                if (abs($difference) >= 0.01) {
                    $installmentService = new InstallmentAdjustmentService();
                    $installmentService->adjustForAmountDifference($payment, $difference);
                }

                $responseData = $payment;
                $notificationBody = "تم تطبيق التعديلات على الدفعة رقم #{$payment->id}.";
            } elseif ($editRequest->action === 'delete') {

                $installmentService = new InstallmentAdjustmentService();
                $installmentService->adjustForAmountDifference(
                    $payment,
                    $payment->amount_usd * -1
                );

                $paymentId = $payment->id;
                $payment->delete();

                $responseData = null;
                $notificationBody = "تمت الموافقة على حذف الدفعة رقم #{$paymentId}.";
            } else {
                return $this->error('نوع طلب التعديل غير مدعوم', 400);
            }

            // حذف طلب التعديل
            $editRequest->delete();
            DB::commit();

            // ============================
            // إرسال الإشعارات
            // ============================

            $firebase = app(\App\Services\FirebaseService::class);
            $title = 'تمت الموافقة على الطلب';

            // 1️⃣ مقدم الطلب
            if ($editRequest->requester && $editRequest->requester->fcmTokens) {
                $tokens = $editRequest->requester->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $notificationBody, [
                        'payment_id' => $payment->id ?? null,
                        'action' => 'edit_request_approved',
                    ]);
                }
            }

            // 2️⃣ الطالب
            $studentUser = $payment->enrollmentContract->student->user ?? null;
            if ($studentUser && $studentUser->fcmTokens) {
                $tokens = $studentUser->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $notificationBody, [
                        'payment_id' => $payment->id ?? null,
                        'action' => 'edit_request_approved',
                    ]);
                }
            }

            // 3️⃣ ولي الأمر
            $familyUser = $payment->enrollmentContract->student->family->user ?? null;
            if ($familyUser && $familyUser->fcmTokens) {
                $tokens = $familyUser->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $notificationBody, [
                        'payment_id' => $payment->id ?? null,
                        'action' => 'edit_request_approved',
                    ]);
                }
            }

            return $this->successResponse(
                $responseData,
                'تمت الموافقة على الطلب وتنفيذ الإجراء بنجاح',
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error(
                'حدث خطأ أثناء الموافقة على الطلب: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * @OA\Put(
     *     path="/api/payments/edit-requests/{id}/reject",
     *     summary="رفض طلب تعديل أو حذف دفعة",
     *     description="
    يقوم هذا المسار برفض طلب تعديل أو حذف دفعة مالية.

    🧠 **السلوك:**
    - يتم رفض الطلب سواء كان:
    - `action = update`
    - `action = delete`
    - لا يتم تطبيق أي تغييرات على الدفعة أو الأقساط.
    - يتم حذف طلب التعديل من جدول `payment_edit_requests`.

    📨 **الإشعارات:**
    - يتم إرسال إشعار إلى:
    - مقدم الطلب
    - الطالب
    - ولي الأمر
    ",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف طلب التعديل",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم رفض الطلب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="تم رفض الطلب وحذفه بنجاح"
     *             ),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="طلب التعديل تمت معالجته مسبقاً",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="طلب التعديل تمت معالجته مسبقاً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="طلب التعديل غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="طلب التعديل غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء رفض الطلب"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function rejectEditRequest($id)
    {
        try {
            $editRequest = PaymentEditRequest::with(
                'payment.enrollmentContract.student.user.fcmTokens',
                'payment.enrollmentContract.student.family.user.fcmTokens',
                'requester.fcmTokens'
            )->find($id);

            if (!$editRequest) {
                return $this->error('طلب التعديل غير موجود', 404);
            }

            if ($editRequest->status !== 'pending') {
                return $this->error('طلب التعديل تمت معالجته مسبقاً', 400);
            }

            $paymentId = $editRequest->payment_id;

            // حذف طلب التعديل بدون تطبيق أي تغيير
            $editRequest->delete();

            // ============================
            // إرسال الإشعارات
            // ============================

            $firebase = app(\App\Services\FirebaseService::class);
            $title = 'تم رفض الطلب';

            $actionText = $editRequest->action === 'delete'
                ? 'حذف'
                : 'تعديل';

            $body = "تم رفض طلب {$actionText} الدفعة رقم #{$paymentId}.";

            // 1️⃣ مقدم الطلب
            if ($editRequest->requester && $editRequest->requester->fcmTokens) {
                $tokens = $editRequest->requester->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, [
                        'payment_id' => $paymentId,
                        'action' => 'edit_request_rejected',
                    ]);
                }
            }

            // 2️⃣ الطالب
            $studentUser = $editRequest->payment->enrollmentContract->student->user ?? null;
            if ($studentUser && $studentUser->fcmTokens) {
                $tokens = $studentUser->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, [
                        'payment_id' => $paymentId,
                        'action' => 'edit_request_rejected',
                    ]);
                }
            }

            // 3️⃣ ولي الأمر
            $familyUser = $editRequest->payment->enrollmentContract->student->family->user ?? null;
            if ($familyUser && $familyUser->fcmTokens) {
                $tokens = $familyUser->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, [
                        'payment_id' => $paymentId,
                        'action' => 'edit_request_rejected',
                    ]);
                }
            }

            return $this->successResponse(
                null,
                'تم رفض الطلب وحذفه بنجاح',
                200
            );
        } catch (\Exception $e) {
            return $this->error(
                'حدث خطأ أثناء رفض الطلب: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/payments/latest-per-student",
     *     summary="جلب الدفعات المالية حسب الطالب أو الشعبة أو الفرع",
     *     description="
     * هذا المسار مخصص **لعرض الدفعات المالية** اعتمادًا على الفلاتر التالية:
     *
     * 1️⃣ إذا لم يُرسل أي فلتر → يتم إرجاع **آخر دفعة لكل طالب**.  
     * 2️⃣ إذا تم تحديد `batch_id` فقط → **آخر دفعة لكل طالب ضمن هذه الشعبة**.  
     * 3️⃣ إذا تم تحديد `student_id` → **جميع الدفعات لهذا الطالب**.  
     * 4️⃣ يمكن استخدام `institute_branch_id` مع أي حالة لتقييد النتائج بفرع محدد.
     *
     * 💡 **البيانات المعادة لكل دفعة:**  
     * - معرّف الدفعة.  
     * - معرّف الطالب.  
     * - الاسم الأول.  
     * - الكنية.  
     * - قيمة الدفعة بالدولار.  
     * - تاريخ الدفع.  
     * - رقم الإيصال.  
     * - ملاحظة الدفعة.
     *
     * ⚠ ملاحظات:
     * - يتم إرجاع الدفعات التي تحتوي على `paid_date` فقط.
     * - إذا لم يكن لدى الطالب أي دفعة مدفوعة → لا يتم إرجاعه.
     * ",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         required=false,
     *         description="معرّف الطالب (ID). عند إرساله يتم جلب جميع الدفعات الخاصة بهذا الطالب.",
     *         @OA\Schema(type="integer", example=12)
     *     ),
     *
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="query",
     *         required=false,
     *         description="معرّف الشعبة (ID). عند إرساله يتم جلب آخر دفعة لكل طالب ضمن هذه الشعبة.",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\Parameter(
     *         name="institute_branch_id",
     *         in="query",
     *         required=false,
     *         description="معرّف فرع المعهد (ID). يمكن استخدامه مع أي فلتر لتقييد النتائج بفرع محدد.",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الدفعات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الدفعات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="payment_id", type="integer", example=101),
     *                     @OA\Property(property="student_id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="أحمد"),
     *                     @OA\Property(property="last_name", type="string", example="الحموي"),
     *                     @OA\Property(property="amount_usd", type="number", example=100),
     *                     @OA\Property(property="paid_date", type="string", format="date", example="2023-01-02"),
     *                     @OA\Property(property="receipt_number", type="string", example="R-12345"),
     *                     @OA\Property(property="note", type="string", example="دفعة الفصل الأول")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح – المستخدم غير مسجل دخول",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ غير متوقع في الخادم أثناء جلب الدفعات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء جلب البيانات")
     *         )
     *     )
     * )
     */
    public function latestPaymentsPerStudent(Request $request)
    {
        $batchId           = $request->query('batch_id');
        $studentId         = $request->query('student_id');
        $instituteBranchId = $request->query('institute_branch_id');

        // 1️⃣ في حال تحديد طالب → جميع دفعاته
        if ($studentId) {

            $payments = Payment::with('enrollmentContract.student')
                ->whereHas(
                    'enrollmentContract.student',
                    fn($q) => $q->where('id', $studentId)
                )
                ->whereNotNull('paid_date');

            // فلترة حسب الفرع
            if ($instituteBranchId) {
                $payments->where('institute_branch_id', $instituteBranchId);
            }

            $payments = $payments
                ->orderByDesc('paid_date')
                ->get();
        } else {

            // 2️⃣ آخر دفعة لكل طالب (مع أو بدون فلترة الشعبة والفرع)
            $payments = Payment::with('enrollmentContract.student')
                ->whereNotNull('paid_date');

            // فلترة حسب الفرع
            if ($instituteBranchId) {
                $payments->where('institute_branch_id', $instituteBranchId);
            }

            // فلترة حسب الشعبة
            if ($batchId) {
                $payments->whereHas(
                    'enrollmentContract.student.batchStudents',
                    fn($q) => $q->where('batch_id', $batchId)
                );
            }

            // ترتيب حسب تاريخ الدفع
            $payments = $payments
                ->orderByDesc('paid_date')
                ->get();

            // تجميع حسب الطالب وأخذ آخر دفعة فقط
            $payments = $payments
                ->groupBy(fn($payment) => $payment->enrollmentContract->student->id)
                ->map(fn($group) => $group->first())
                ->values();
        }

        // 3️⃣ تجهيز النتيجة النهائية
        $results = $payments->map(fn($payment) => [
            'student_id'      => $payment->enrollmentContract->student->id,
            'payment_id'      => $payment->id,
            'first_name'      => $payment->enrollmentContract->student->first_name,
            'last_name'       => $payment->enrollmentContract->student->last_name,
            'amount_usd'      => $payment->amount_usd,
            'paid_date'       => $payment->paid_date,
            'receipt_number'  => $payment->receipt_number,
            'note'            => $payment->note,
            'institute_branch_id' => $payment->institute_branch_id,
        ]);

        return $this->successResponse(
            $results,
            'تم جلب الدفعات بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/payments/student-late",
     *     summary="الطلاب المتأخرون في سداد الأقساط",
     *     description="يعيد قائمة بالطلاب الذين لديهم أقساط متأخرة في السنة الحالية.
     * يُعتبر القسط متأخرًا إذا كان تاريخ استحقاقه في شهر سابق، أو في الشهر الحالي بعد اليوم الخامس، ولم يتم سداده بعد.
     * - إذا لم يتم إرسال أي فلتر، يتم جلب آخر قسط متأخر لكل طالب إن وجد.
     * - إذا تم اختيار شعبة، يتم جلب آخر قسط متأخر لكل طالب في هذه الشعبة.
     * - إذا تم اختيار طالب، يتم جلب كل الأقساط المتأخرة لهذا الطالب.
     * - إذا تم تمرير باراميتر month، يتم جلب الأقساط المتأخرة فقط للشهر المحدد.",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         required=false,
     *         description="معرّف الطالب لجلب أقساطه المتأخرة فقط",
     *         @OA\Schema(type="integer", example=12)
     *     ),
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="query",
     *         required=false,
     *         description="معرّف الشعبة لجلب آخر قسط متأخر لكل طالب في هذه الشعبة",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         required=false,
     *         description="رقم الشهر لجلب الأقساط المتأخرة فقط لذلك الشهر (1 = يناير، 12 = ديسمبر)",
     *         @OA\Schema(type="integer", example=11)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الطلاب وأقساطهم المتأخرة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الطلاب وأقساطهم المتأخرة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="student_id", type="integer", example=15),
     *                     @OA\Property(property="student_name", type="string", example="خالد أحمد"),
     *                     @OA\Property(
     *                         property="late_installments",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="installment_id", type="integer", example=42),
     *                             @OA\Property(property="due_date", type="string", format="date", example="2025-11-01"),
     *                             @OA\Property(property="amount", type="number", format="float", example=150.00),
     *                             @OA\Property(property="status", type="string", example="pending")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="غير مصادق عليه"),
     *     @OA\Response(response=403, description="غير مصرح للوصول")
     * )
     */
    public function lateStudentsInPayment(Request $request)
    {
        $studentId = $request->query('student_id');
        $batchId = $request->query('batch_id');
        $monthFilter = $request->query('month'); // الشهر الجديد
        $today = now()->day;
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $query = PaymentInstallment::with(['enrollmentContract.student']);

        // فلترة السنة الحالية
        $query->whereYear('due_date', $currentYear);

        // شرط الأقساط المتأخرة
        $query->where(function ($q) use ($today, $currentMonth, $monthFilter) {

            // إذا تم تحديد شهر محدد
            if ($monthFilter) {
                $q->whereMonth('due_date', $monthFilter)
                    ->where('status', '!=', 'paid');
            } else {
                // السلوك الحالي: أي قسط قبل الشهر الحالي أو في الشهر الحالي ولم ينته اليوم
                $q->where(function ($q2) use ($currentMonth) {
                    $q2->whereMonth('due_date', '<', $currentMonth)
                        ->where('status', '!=', 'paid');
                })
                    ->orWhere(function ($q3) use ($today, $currentMonth) {
                        $q3->whereMonth('due_date', $currentMonth)
                            ->whereDay('due_date', '<=', $today)
                            ->where('status', '!=', 'paid');
                    });
            }
        });

        // فلترة حسب student_id
        if ($studentId) {
            $query->whereHas('enrollmentContract.student', function ($q) use ($studentId) {
                $q->where('id', $studentId);
            });
        }

        // فلترة حسب batch_id
        if ($batchId) {
            $query->whereHas('enrollmentContract.student.latestBatchStudent', function ($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            });
        }

        $installments = $query->get();

        $students = [];

        foreach ($installments as $installment) {

            $student = $installment->enrollmentContract->student ?? null;
            if (!$student) continue;

            // lazy load للفرع
            $instituteBranch = $student->instituteBranch;

            $studentId = $student->id;

            if (!isset($students[$studentId])) {

                $students[$studentId] = [
                    'student_id' => $studentId,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'institute_branch' => $instituteBranch ? [
                        'id' => $instituteBranch->id,
                        'name' => $instituteBranch->name,
                    ] : null,

                    'late_installments' => [],
                ];
            }

            $students[$studentId]['late_installments'][] = [
                'installment_id' => $installment->id,
                'due_date' => $installment->due_date,
                'amount' => (float)$installment->planned_amount_usd - (float)($installment->paid_amount_usd ?? 0),
                'status' => $installment->status,
            ];
        }

        $students = array_values($students);

        if (!$studentId && !$batchId) {
            $students = collect($students)->map(function ($s) {
                $s['late_installments'] = collect($s['late_installments'])
                    ->sortByDesc('due_date')
                    ->take(1)
                    ->values()
                    ->all();
                return $s;
            })->values()->all();
        }

        return $this->successResponse(
            $students,
            'تم جلب الطلاب وأقساطهم المتأخرة بنجاح',
            200
        );
    }


    /**
     * @OA\Get(
     *     path="/api/payments/edit-requests",
     *     summary="Get all payment edit requests",
     *     description="Fetches all edit requests for all payments, including associated payment details.",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All edit requests retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="All edit requests fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="payment_id", type="integer", example=1),
     *                     @OA\Property(property="requester_id", type="integer", example=5),
     *                     @OA\Property(property="original_data", type="object"),
     *                     @OA\Property(property="proposed_changes", type="object"),
     *                     @OA\Property(property="reason", type="string", example="Correct wrong amount"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-16T10:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-16T11:00:00.000000Z"),
     *                     @OA\Property(property="payment", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء جلب طلبات تعديل الدفعات"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function getAllEditRequests()
    {
        try {
            // جلب كل طلبات التعديل لكل الدفعات
            $editRequests = PaymentEditRequest::with('payment')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse(
                $editRequests,
                'تم جلب كل طلبات تعديل الدفعات بنجاح',
                200
            );
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء جلب طلبات تعديل الدفعات', 500);
        }
    }
}
