<?php

namespace Modules\Audits\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Audits\Models\Audit;
use Modules\Audits\Services\AuditValuePresenter;
use Modules\Shared\Traits\SuccessResponseTrait;

class AuditsController extends Controller
{
    use SuccessResponseTrait;

    public function __construct(
        private readonly AuditValuePresenter $auditValuePresenter
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/audits",
     *     operationId="getAudits",
     *     tags={"Audits"},
     *     summary="جلب جميع سجلات التدقيق (Audit Logs)",
     *     description="
     * يقوم هذا المسار بإرجاع جميع سجلات التدقيق في النظام.
     *
     * كل سجل تدقيق يحتوي على:
     * - نوع العملية (event)
     * - المستخدم الذي قام بالعملية (إن وجد)
     * - الكيان المتأثر (auditable)
     * - القيم القديمة والجديدة بعد إخفاء الحقول الحساسة
     *
     * 🛡️ **ملاحظات أمنية:**
     * - يتم إخفاء الحقول الحساسة مثل (email, phone, national_id).
     * - في حال عدم وجود مستخدم مرتبط بالسجل، يتم إرجاع `N/A`.
     * ",
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع سجلات التدقيق بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع سجلات التدقيق بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="event", type="string", example="updated"),
     *                     @OA\Property(property="user_id", type="integer", nullable=true, example=5),
     *                     @OA\Property(property="user_name", type="string", example="Ahmad Ali"),
     *                     @OA\Property(property="auditable_type", type="string", example="Modules\\Users\\Models\\User"),
     *                     @OA\Property(property="auditable_id", type="integer", example=12),
     *                     @OA\Property(
     *                         property="old_values",
     *                         type="object",
     *                         example={"email":"***","phone":"***"}
     *                     ),
     *                     @OA\Property(
     *                         property="new_values",
     *                         type="object",
     *                         example={"email":"***","phone":"***"}
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-19T10:30:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد أي سجلات تدقيق",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي سجلات متاحة حالياً")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء جلب السجلات.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            // Remove 'auditable' from eager loading to prevent crashes if module classes are missing
            $audits = Audit::with(['user'])->orderBy('created_at', 'desc')->get();

            if ($audits->isEmpty()) {
                return $this->error('لا يوجد أي سجلات متاحة حالياً', 404);
            }

            $result = $audits->map(function ($audit) {
                return [
                    'id' => $audit->id,
                    'event' => $audit->event,
                    'user_id' => $audit->user_id,
                    'user_name' => $audit->user?->name ?? 'N/A',
                    'auditable_type' => $audit->auditable_type,
                    'auditable_id' => $audit->auditable_id,
                    'old_values' => $this->auditValuePresenter->present($audit, $audit->old_values),
                    'new_values' => $this->auditValuePresenter->present($audit, $audit->new_values),
                    'created_at' => $audit->created_at,
                ];
            });

            return $this->successResponse($result, 'تم جلب جميع سجلات التدقيق بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ غير متوقع أثناء جلب السجلات.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/audits/latest",
     *     operationId="getLatestAudits",
     *     tags={"Audits"},
     *     summary="جلب سجلات التدقيق لآخر أسبوعين فقط",
     *     description="يقوم هذا المسار بإرجاع سجلات التدقيق التي تمت خلال آخر 14 يوماً.",
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب سجلات التدقيق للأسبوعين الأخيرين بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب سجلات التدقيق للأسبوعين الأخيرين بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function latest()
    {
        try {
            $twoWeeksAgo = now()->subDays(14);
            // Remove 'auditable' from eager loading to prevent crashes if module classes are missing
            $audits = Audit::with(['user'])
                ->where('created_at', '>=', $twoWeeksAgo)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($audits->isEmpty()) {
                return $this->error('لا يوجد أي سجلات خلال آخر أسبوعين', 404);
            }

            $result = $audits->map(function ($audit) {
                return [
                    'id' => $audit->id,
                    'event' => $audit->event,
                    'user_id' => $audit->user_id,
                    'user_name' => $audit->user?->name ?? 'N/A',
                    'auditable_type' => $audit->auditable_type,
                    'auditable_id' => $audit->auditable_id,
                    'old_values' => $this->auditValuePresenter->present($audit, $audit->old_values),
                    'new_values' => $this->auditValuePresenter->present($audit, $audit->new_values),
                    'created_at' => $audit->created_at,
                ];
            });

            return $this->successResponse($result, 'تم جلب سجلات التدقيق للأسبوعين الأخيرين بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ غير متوقع أثناء جلب السجلات.', 500);
        }
    }

    /**
     * إخفاء الحقول الحساسة في سجلات التدقيق
     */
    private function maskSensitiveFields($values)
    {
        // إذا لم تكن مصفوفة (رغم وجود $casts)، فهذا للسلامة فقط
        if (!is_array($values)) {
            // محاولة فك يدوي في حال فشل الـ cast (نادر)
            $decoded = json_decode($values, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $values = $decoded;
            } else {
                return $values; // أعد كما هو لتجنب كسر الاستجابة
            }
        }

        $sensitiveFields = ['national_id', 'phone', 'email'];

        foreach ($sensitiveFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = '***';
            }
        }

        return $values;
    }
}
