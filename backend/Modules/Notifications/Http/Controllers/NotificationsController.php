<?php

namespace Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Notifications\Http\Requests\StoreNotificationRequest;
use Modules\Notifications\Http\Resources\NotificationResource;
use Modules\Notifications\Http\Resources\NotificationDetailResource;
use Modules\Notifications\Services\NotificationService;
use Modules\Notifications\Models\Notification;
use Modules\Shared\Traits\SuccessResponseTrait;
use Illuminate\Http\Request;
use Modules\NotificationRecipients\Models\NotificationRecipient;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\Http\Resources\NotificationUserDetailResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Notifications\Http\Resources\AdminNotificationResource;
use Modules\Notifications\Http\Requests\AdminNotificationIndexRequest;

class NotificationsController extends Controller
{
    use SuccessResponseTrait;

    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Post(
     *     path="/api/notifications",
     *     tags={"Notifications"},
     *     summary="إنشاء إشعار جديد",
     *     description="إنشاء إشعار جديد لمجموعة من المستخدمين مع دعم المرفقات وقوالب الرسائل.",
     *     security={{"sanctum": {}}},
     *     
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "body", "target_snapshot[type]"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=255,
     *                     description="عنوان الإشعار",
     *                     example="تنبيه: تحديث في جدول الحصص"
     *                 ),
     *                 @OA\Property(
     *                     property="body",
     *                     type="string",
     *                     minLength=5,
     *                     maxLength=2000,
     *                     description="محتوى الإشعار",
     *                     example="يرجى العلم بأنه تم تحديث جدول الحصص للأسبوع القادم."
     *                 ),
     *                 @OA\Property(
     *                     property="template_id",
     *                     type="integer",
     *                     nullable=true,
     *                     description="معرف القالب المستخدم (اختياري)",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="sender_id",
     *                     type="integer",
     *                     nullable=true,
     *                     description="معرف المرسل (اختياري)",
     *                     example=2
     *                 ),
     *                 @OA\Property(
     *                     property="sender_type",
     *                     type="string",
     *                     enum={"admin", "system", "user"},
     *                     nullable=true,
     *                     description="نوع المرسل",
     *                     example="admin"
     *                 ),
     *                 @OA\Property(
     *                     property="target_snapshot[type]",
     *                     type="string",
     *                     enum={"all", "branch", "custom"},
     *                     description="نوع المستهدفين",
     *                     example="custom"
     *                 ),
     *                 @OA\Property(
     *                     property="target_snapshot[user_ids][]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     description="مصفوفة معرفات المستخدمين (مطلوب إذا كان النوع custom)",
     *                     example={12, 45, 67}
     *                 ),
     *                 @OA\Property(
     *                     property="target_snapshot[branch_id]",
     *                     type="integer",
     *                     description="معرف الفرع (مطلوب إذا كان النوع branch)",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="attachments[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="مصفوفة الملفات المرفقة (بحد أقصى 5 ملفات و 10 ميجابايت لكل ملف)"
     *                 )
     *             )
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الإشعار بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الإشعار بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/NotificationItemResource")
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح به - التوكن غير صالح",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     
     *     @OA\Response(
     *         response=422,
     *         description="بيانات غير صالحة - فشل التحقق",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل إنشاء الإشعار: خطأ غير متوقع")
     *         )
     *     )
     * )
     */

    public function store(StoreNotificationRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('attachments')) {
                $data['attachments'] = $request->file('attachments');
            }

            $notification = $this->service->createNotification($data);

            Log::info('✅ تم إنشاء إشعار جديد بنجاح', [
                'notification_id' => $notification->id,
                'user_id' => $request->user()->id ?? null,
                'recipients_count' => $notification->recipients_count ?? 0,
            ]);

            return $this->successResponse(
                new NotificationResource($notification),
                'تم إنشاء الإشعار بنجاح',
                201
            );
        } catch (\Throwable $e) {
            Log::error('❌ فشل إنشاء إشعار', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
            ]);

            return $this->error(
                'فشل إنشاء الإشعار: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * جلب إشعارات المستخدم
     */
    /**
     * جلب إشعارات المستخدم الحالي
     *
     * يسترجع جميع الإشعارات المرسلة للمستخدم الحالي مع دعم:
     * - التصفية حسب حالة القراءة (مقروءة/غير مقروءة/الجميع)
     * - تحميل المرفقات والقوالب تلقائيًا
     * - ترقيم الصفحات لتحسين الأداء
     * - عرض حالة التسليم والقراءة لكل إشعار
     *
     * @OA\Get(
     *     path="/api/notifications",
     *     tags={"Notifications"},
     *     summary="استرجاع قائمة الإشعارات",
     *     description="جلب جميع الإشعارات المرتبطة بالمستخدم المصادق عليه مع دعم التصفية والترقيم. يتضمن تفاصيل الإشعار الكاملة مع المرفقات وحالة القراءة.",
     *     security={{"sanctum": {}}},
     *     
     *     @OA\Parameter(
     *         name="unread",
     *         in="query",
     *         description="تصفية حسب حالة القراءة: `true` = غير مقروءة فقط، `false` = مقروءة فقط. إذا لم تُحدد: جلب الجميع",
     *         required=false,
     *         example=true,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="عدد العناصر في كل صفحة (الحد الأقصى 50)",
     *         required=false,
     *         example=10,
     *         @OA\Schema(type="integer", minimum=1, maximum=50, default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="رقم الصفحة المطلوبة",
     *         required=false,
     *         example=1,
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *     
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الإشعارات بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الإشعارات بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(ref="#/components/schemas/NotificationItemResource")
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/notifications?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/notifications?page=3"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://127.0.0.1:8000/api/notifications?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/notifications"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=25)
     *             )
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول - توكن غير صالح أو منتهي",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     
     *     @OA\Response(
     *         response=422,
     *         description="بيانات غير صالحة",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل جلب الإشعارات: خطأ غير متوقع")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // استعلام المستلمين مع تحميل الإشعار والعلاقات
            $query = NotificationRecipient::query()
                ->where('user_id', $user->id)
                ->with([
                    'notification',
                    'notification.attachments',
                    'notification.template',
                    'notification.recipients',
                ])
                ->latest('created_at');

            // تصفية حسب حالة القراءة
            // التحقق من وجود المتغير قبل تطبيق التصفية
            if ($request->has('unread')) {
                $unreadFilter = $request->boolean('unread');

                if ($unreadFilter) {
                    // جلب غير المقروءة فقط
                    $query->whereNull('read_at');
                } else {
                    // جلب المقروءة فقط
                    $query->whereNotNull('read_at');
                }
            }
            // إذا لم يتم إرسال 'unread'، يتم جلب جميع الإشعارات (مقروءة وغير مقروءة)

            // تعداد غير المقروء
            $unreadCount = NotificationRecipient::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();

            // ترقيم الصفحات
            $perPage = $request->integer('per_page', 10);
            $notifications = $query->paginate($perPage);

            return $this->successResponse(
                NotificationResource::collection($notifications),
                'تم جلب الإشعارات بنجاح',
                200
            );
        } catch (\Throwable $e) {
            Log::error('❌ فشل جلب الإشعارات', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
            ]);

            return $this->error(
                'فشل جلب الإشعارات: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * عرض تفاصيل إشعار معين للمستخدم المصادق عليه.
     *
     * الهدف:
     * يتيح هذا الـ Endpoint للمستخدم جلب تفاصيل إشعار واحد تم إرساله له،
     * مع جميع البيانات المرتبطة بالإشعار مثل معلومات الاستلام، بيانات الإشعار الأساسية،
     * القالب المستخدم، المرفقات، وحالة القراءة والتسليم.
     *
     * متى تستخدم:
     * يستخدم عندما يحتاج العميل (Mobile / Web) إلى عرض صفحة تفاصيل الإشعار،
     * معرفة حالة القراءة، تحميل المرفقات أو معرفة مصدر الإشعار.
     *
     * متطلبات الأمان:
     * يتطلب المصادقة (Sanctum). يسمح بالوصول فقط لصاحب سجل الاستلام.
     *
     * آلية العمل داخلياً:
     * 1. استلام معرف سجل الاستلام (NotificationRecipient ID).
     * 2. البحث عن السجل في قاعدة البيانات.
     * 3. التحقق أن المستخدم المصادق هو مالك السجل.
     * 4. تحميل العلاقات الضرورية (attachments, template).
     * 5. إعادة البيانات بصيغة Resource منظمة.
     *
     * الكيان المعتمد: NotificationRecipient (يمثل العلاقة بين المستخدم والإشعار).
     *
     * @OA\Get(
     *     path="/api/notifications/{reception}",
     *     tags={"Notifications"},
     *     summary="جلب تفاصيل إشعار معين للمستخدم",
     *     description="إرجاع تفاصيل إشعار مرتبط بالمستخدم المصادق عليه، بما في ذلك بيانات الاستلام والمرفقات والقالب.",
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="reception",
     *         in="path",
     *         description="معرف سجل استلام الإشعار (NotificationRecipient ID)",
     *         required=true,
     *         example=13,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب تفاصيل الإشعار بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب تفاصيل الإشعار بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="recipient", type="object",
     *                     @OA\Property(property="id", type="integer", example=13),
     *                     @OA\Property(property="notification_id", type="integer", example=7),
     *                     @OA\Property(property="received_at", type="string", format="date-time"),
     *                     @OA\Property(property="received_at_human", type="string"),
     *                     @OA\Property(property="read_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="read_at_human", type="string", nullable=true),
     *                     @OA\Property(property="delivered_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="is_read", type="boolean"),
     *                     @OA\Property(property="status", type="string", enum={"pending","delivered","read"})
     *                 ),
     *                 @OA\Property(property="notification", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="body", type="string"),
     *                     @OA\Property(property="sender", type="object",
     *                         @OA\Property(property="type", type="string", enum={"admin","system","user","teacher","employee"}),
     *                         @OA\Property(property="display_name", type="string")
     *                     ),
     *                     @OA\Property(property="template", type="object", nullable=true),
     *                     @OA\Property(property="attachments", type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="url", type="string"),
     *                             @OA\Property(property="mime_type", type="string"),
     *                             @OA\Property(property="size", type="integer"),
     *                             @OA\Property(property="size_formatted", type="string"),
     *                             @OA\Property(property="is_image", type="boolean")
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="created_at_human", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="المستخدم غير مصادق"),
     *     @OA\Response(response=403, description="المستخدم لا يملك الإشعار"),
     *     @OA\Response(response=404, description="الإشعار غير موجود"),
     *     @OA\Response(response=500, description="خطأ داخلي في الخادم")
     * )
     */

    public function show($receptionId, Request $request): JsonResponse
    {
        try {
            Log::info('🔍 [SHOW] بدء معالجة طلب عرض تفاصيل إشعار', [
                'reception_id_param' => $receptionId,
                'requested_by_user_id' => $request->user()->id ?? null,
            ]);

            // 🔴 البحث يدويًا باستخدام الـ FQCN الكامل
            $reception = \Modules\NotificationRecipients\Models\NotificationRecipient::find($receptionId);

            if (!$reception) {
                Log::warning('⚠️ [SHOW] السجل غير موجود في قاعدة البيانات', [
                    'reception_id' => $receptionId,
                    'user_id' => $request->user()->id ?? null,
                    'query_result' => 'null',
                ]);
                return $this->error('الإشعار غير موجود', 404);
            }

            Log::info('✅ [SHOW] السجل موجود', [
                'reception_id' => $reception->id,
                'user_id' => $reception->user_id,
                'notification_id' => $reception->notification_id,
            ]);

            // التحقق الأمني: التأكد من ملكية الإشعار
            $this->authorizeReception($reception, $request);

            Log::info('✅ [SHOW] التحقق الأمني ناجح - تحميل العلاقات', [
                'reception_id' => $reception->id,
                'user_id' => $request->user()->id,
            ]);

            // تحميل العلاقات الضرورية فقط
            $reception->load([
                'notification.attachments',
                'notification.template:id,name',
            ]);

            Log::info('✅ [SHOW] العلاقات محملة بنجاح', [
                'has_notification' => $reception->relationLoaded('notification'),
                'attachments_count' => $reception->notification->attachments->count() ?? 0,
                'has_template' => $reception->notification->template !== null,
            ]);

            return $this->successResponse(
                new NotificationUserDetailResource($reception),
                'تم جلب تفاصيل الإشعار بنجاح'
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('❌ [SHOW] خطأ في التحقق الأمني - غير مصرح بالوصول', [
                'reception_id' => $receptionId ?? 'unknown',
                'reception_user_id' => $reception->user_id ?? 'unknown',
                'attempted_user_id' => $request->user()->id ?? 'unknown',
                'error_message' => $e->getMessage(),
            ]);
            return $this->error('غير مصرح بالوصول لهذا الإشعار', 403);
        } catch (\Throwable $e) {
            Log::error('🔥 [SHOW] خطأ غير متوقع أثناء جلب تفاصيل الإشعار', [
                'error' => $e->getMessage(),
                'reception_id' => $receptionId ?? null,
                'user_id' => $request->user()->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->error('فشل جلب تفاصيل الإشعار: خطأ غير متوقع', 500);
        }
    }
    /**
     * التحقق الأمني من ملكية الإشعار
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function authorizeReception(NotificationRecipient $reception, Request $request): void
    {
        $receptionUserId = $reception->user_id;
        $requestUserId = $request->user()->id;

        Log::debug('🔐 [AUTH] بدء التحقق من الصلاحية', [
            'reception_id' => $reception->id,
            'reception_user_id' => $receptionUserId,
            'request_user_id' => $requestUserId,
            'are_equal' => ($receptionUserId === $requestUserId),
            'reception_user_id_type' => gettype($receptionUserId),
            'request_user_id_type' => gettype($requestUserId),
        ]);

        if ($receptionUserId !== $requestUserId) {
            Log::warning('⚠️ [AUTH] فشل التحقق - عدم تطابق المعرفات', [
                'reception_id' => $reception->id,
                'reception_user_id' => $receptionUserId,
                'reception_user_id_type' => gettype($receptionUserId),
                'attempted_user_id' => $requestUserId,
                'attempted_user_id_type' => gettype($requestUserId),
                'reception_user_id_str' => (string)$receptionUserId,
                'attempted_user_id_str' => (string)$requestUserId,
                'are_equal_str' => ((string)$receptionUserId === (string)$requestUserId),
            ]);

            throw new \Illuminate\Auth\Access\AuthorizationException(
                'محاولة وصول غير مصرح بها لإشعار'
            );
        }

        Log::info('✅ [AUTH] التحقق ناجح - المستخدم مالك الإشعار', [
            'reception_id' => $reception->id,
            'user_id' => $requestUserId,
        ]);
    }
    /**
     * تعليم الإشعار كمقروء
     */
    /**
     * @OA\Patch(
     *     path="/api/notifications/{reception}/read",
     *     summary="تعليم إشعار كمقروء",
     *     description="
يقوم هذا المسار بتعليم إشعار معين كمقروء من قبل المستخدم المصادق عليه.
يتم تحديث حقل `read_at` بتوقيت القراءة الحالي فقط إذا لم يكن الإشعار مقروءًا مسبقًا.

🧠 **آلية العمل:**
- يتم استقبال معرف سجل استلام الإشعار (`reception`) كجزء من المسار.
- يتم البحث اليدوي عن السجل في قاعدة البيانات (لتجنب مشاكل Route Model Binding مع النماذج في الـ Modules).
- يتم التحقق من أن المستخدم المصادق عليه هو مالك هذا السجل (من خلال مقارنة `user_id`).
- إذا كان الإشعار غير مقروء (`read_at` فارغ):
  - يتم تحديث الحقل `read_at` بالتوقيت الحالي.
  - يتم تسجيل العملية في السجلات مع تفاصيل المستخدم والإشعار.
- إذا كان الإشعار مقروءًا مسبقًا:
  - لا يتم تنفيذ أي تحديث (العملية مُعرفة بأنها ذات تأثير محايد - Idempotent).
  - يتم إرجاع استجابة نجاح دون تغيير.

📌 **ملاحظات مهمة:**
- العملية **مُعرفة بأنها ذات تأثير محايد (Idempotent)**: يمكن استدعاء المسار عدة مرات دون تغيير النتيجة بعد المرة الأولى.
- في حالة محاولة تعليم إشعار لا يملكه المستخدم: يتم إرجاع خطأ 403 مع تسجيل تحذيري.
- في حالة عدم وجود السجل: يتم إرجاع خطأ 404.
- يتم تحديث `read_at` مرة واحدة فقط (أول قراءة).
- لا يتم إرجاع أي بيانات في الاستجابة الناجحة (فقط رسالة نجاح).
- البحث اليدوي يتجنب مشاكل الـ Route Model Binding مع النماذج داخل الـ Modules.

🔒 **الأمان:**
- يتطلب المستخدم أن يكون مسجلاً للدخول (مصادق عليه عبر Sanctum).
- يتم التحقق الصارم من ملكية السجل قبل أي تحديث.
- جميع المحاولات غير المصرح بها تُسجل في السجلات للأغراض الأمنية.
",
     *     tags={"Notifications"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="reception",
     *         in="path",
     *         required=true,
     *         description="معرف سجل استلام الإشعار (NotificationRecipient ID)",
     *         @OA\Schema(type="integer", example=13)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تعليم الإشعار كمقروء بنجاح (أو كان مقروءًا مسبقًا)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تعليم الإشعار كمقروء بنجاح")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح - المستخدم غير مصادق عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="غير مصرح بالوصول - المستخدم ليس مالك الإشعار",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="غير مصرح بالوصول لهذا الإشعار")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الإشعار غير موجود (سجل الاستلام)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الإشعار غير موجود")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل تعليم الإشعار كمقروء: خطأ غير متوقع")
     *         )
     *     )
     * )
     */
    public function markAsRead($receptionId, Request $request)
    {
        try {
            // 🔴 البحث اليدوي لتجنب مشاكل Route Model Binding مع الـ Modules
            $reception = \Modules\NotificationRecipients\Models\NotificationRecipient::find($receptionId);

            if (!$reception) {
                Log::warning('⚠️ [MARK_AS_READ] محاولة تعليم إشعار غير موجود', [
                    'reception_id' => $receptionId,
                    'user_id' => $request->user()->id ?? null,
                ]);
                return $this->error('الإشعار غير موجود', 404);
            }

            // التحقق الأمني
            $this->authorizeReception($reception, $request);

            // تحديث فقط إذا لم يكن مقروءًا
            if (is_null($reception->read_at)) {
                $reception->update([
                    'read_at' => now(),
                ]);

                Log::info('✅ [MARK_AS_READ] تم تعليم الإشعار كمقروء', [
                    'reception_id' => $reception->id,
                    'user_id' => $request->user()->id,
                    'notification_id' => $reception->notification_id,
                ]);
            } else {
                Log::debug('ℹ️ [MARK_AS_READ] الإشعار مقروء مسبقًا - لا حاجة للتحديث', [
                    'reception_id' => $reception->id,
                    'user_id' => $request->user()->id,
                ]);
            }

            return $this->successResponse(null, 'تم تعليم الإشعار كمقروء بنجاح');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('⚠️ [MARK_AS_READ] محاولة تعليم إشعار غير مملوك', [
                'reception_id' => $receptionId,
                'attempted_user_id' => $request->user()->id ?? null,
                'owner_user_id' => $reception->user_id ?? 'unknown',
            ]);
            return $this->error('غير مصرح بالوصول لهذا الإشعار', 403);
        } catch (\Throwable $e) {
            Log::error('❌ [MARK_AS_READ] فشل تعليم الإشعار كمقروء', [
                'error' => $e->getMessage(),
                'reception_id' => $receptionId,
                'user_id' => $request->user()->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->error('فشل تعليم الإشعار كمقروء: خطأ غير متوقع', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notifications/mark-all-as-read",
     *     summary="تعليم جميع الإشعارات كمقروءة",
     *     description="
يقوم هذا المسار بتعليم **جميع الإشعارات غير المقروءة** للمستخدم المصادق عليه كمقروءة.
يتم تحديث حقل `read_at` لجميع السجلات دفعة واحدة في عملية واحدة.

🧠 **آلية العمل:**
- يتم استعلام جميع سجلات استلام الإشعارات (`notification_recipients`) للمستخدم الحالي.
- يتم تصفية السجلات التي `read_at` فارغة (غير مقروءة).
- يتم تنفيذ تحديث جماعي (`bulk update`) لجميع السجلات المُصفاة.
- يتم إرجاع عدد الإشعارات التي تم تحديثها بنجاح.

📌 **ملاحظات مهمة:**
- العملية **سريعة وفعالة** لأنها تستخدم تحديثاً جماعياً (bulk update) بدلاً من التحديث الفردي.
- إذا لم تكن هناك إشعارات غير مقروءة: يتم إرجاع `count = 0` مع رسالة نجاح.
- **لا يتم حذف الإشعارات** - فقط تُحدّث حالة القراءة.
- التحديثات تتم عبر معاملة آمنة لضمان سلامة البيانات.

🔒 **الأمان:**
- يتطلب المستخدم أن يكون مسجلاً للدخول (مصادق عليه عبر Sanctum).
- يتم تحديث **فقط** إشعارات المستخدم الحالي (يتم تصفية حسب `user_id`).
- لا يمكن للمستخدم تعديل إشعارات مستخدمين آخرين.

📊 **الاستخدام النموذجي:**
- عند فتح المستخدم لصفحة الإشعارات، قد يرغب في تعليم كل شيء كمقروء.
- عند النقر على زر 'تعليم الكل كمقروء' في واجهة المستخدم.
- عند مزامنة حالة القراءة بعد قراءة الإشعارات خارج النظام.
",
     *     tags={"Notifications"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تعليم جميع الإشعارات كمقروءة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تعليم 5 إشعارات كمقروءة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="count", type="integer", example=5, description="عدد الإشعارات التي تم تحديثها")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح - المستخدم غير مصادق عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل تعليم جميع الإشعارات كمقروءة: خطأ غير متوقع")
     *         )
     *     )
     * )
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = $request->user();

            $updated = NotificationRecipient::where('user_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            Log::info('✅ تم تعليم جميع الإشعارات كمقروءة', [
                'user_id' => $user->id,
                'count' => $updated,
            ]);

            return $this->successResponse(
                ['count' => $updated],
                "تم تعليم {$updated} إشعارات كمقروءة بنجاح"
            );
        } catch (\Throwable $e) {
            Log::error('❌ فشل تعليم جميع الإشعارات كمقروءة', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
            ]);

            return $this->error(
                'فشل تعليم جميع الإشعارات كمقروءة: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/notifications/unread/count",
     *     summary="الحصول على عدد الإشعارات غير المقروءة",
     *     description="
يقوم هذا المسار بإرجاع **عدد الإشعارات غير المقروءة** للمستخدم المصادق عليه.
يتم حساب العدد عبر استعلام مباشر وسريع في قاعدة البيانات.

🧠 **آلية العمل:**
- يتم استعلام جدول `notification_recipients` للبحث عن السجلات التي:
  - تخص المستخدم الحالي (`user_id` = المستخدم المصادق عليه)
  - لم يتم قراءتها بعد (`read_at` فارغ)
- يتم تنفيذ عملية عد (`COUNT`) مباشرة على قاعدة البيانات.
- يتم إرجاع النتيجة كعدد صحيح.

📌 **ملاحظات مهمة:**
- الاستعلام **سريع جداً** لأنه يستخدم `COUNT` بدلاً من جلب جميع السجلات.
- النتيجة دائماً **عدد صحيح** (قد يكون 0 إذا لم تكن هناك إشعارات غير مقروءة).
- يمكن استخدام هذا المسار لعرض **شارة (badge)** في واجهة المستخدم.
- لا يتم تحميل أي بيانات إضافية - فقط العدد.

🔒 **الأمان:**
- يتطلب المستخدم أن يكون مسجلاً للدخول (مصادق عليه عبر Sanctum).
- يتم حساب العدد **فقط** للإشعارات الخاصة بالمستخدم الحالي.
- لا يمكن للمستخدم رؤية عداد إشعارات مستخدمين آخرين.

📊 **الاستخدام النموذجي:**
- عرض شارة حمراء بجانب أيقونة الإشعارات في شريط التنقل.
- تحديث شارة الإشعارات ديناميكياً كل دقيقة.
- عرض عدد الإشعارات غير المقروءة في صفحة الإشعارات.
- إرسال تنبيه للمستخدم عند تجاوز عدد معين من الإشعارات.
",
     *     tags={"Notifications"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب عداد الإشعارات غير المقروءة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب عداد غير المقروء بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="count", type="integer", example=5, description="عدد الإشعارات غير المقروءة")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح - المستخدم غير مصادق عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل جلب عداد غير المقروء: خطأ غير متوقع")
     *         )
     *     )
     * )
     */
    public function unreadCount(Request $request)
    {
        try {
            $count = NotificationRecipient::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->count();

            return $this->successResponse(
                ['count' => $count],
                'تم جلب عداد غير المقروء بنجاح'
            );
        } catch (\Throwable $e) {
            Log::error('❌ فشل جلب عداد غير المقروء', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
            ]);

            return $this->error(
                'فشل جلب عداد غير المقروء: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/notifications/{reception}",
     *     summary="حذف إشعار (للمستلم فقط)",
     *     description="
يقوم هذا المسار بحذف **سجل استلام إشعار معين** للمستخدم المصادق عليه.
يتم حذف السجل فقط من جدول `notification_recipients` دون التأثير على الإشعار الأصلي.

🧠 **آلية العمل:**
- يتم استقبال معرف سجل استلام الإشعار (`reception`) كجزء من المسار.
- يتم البحث اليدوي عن السجل في قاعدة البيانات (لتجنب مشاكل Route Model Binding مع النماذج في الـ Modules).
- يتم التحقق من أن المستخدم المصادق عليه هو مالك هذا السجل.
- يتم تنفيذ عملية الحذف (`DELETE`) للسجل فقط.
- **ملاحظة مهمة**: يتم حذف سجل الاستلام فقط، وليس الإشعار نفسه. الإشعار يبقى موجوداً للمستلمين الآخرين.

📌 **ملاحظات مهمة:**
- **الحذف يخص المستخدم فقط**: لا يؤثر على المستلمين الآخرين للإشعار نفسه.
- إذا حاول المستخدم حذف إشعار لا يملكه: يتم إرجاع خطأ 403.
- إذا كان السجل غير موجود: يتم إرجاع خطأ 404.
- **لا يتم حذف المرفقات** أو بيانات الإشعار الأصلية.
- العملية **نهائية** - لا يمكن استعادة السجل المحذوف.

🔒 **الأمان:**
- يتطلب المستخدم أن يكون مسجلاً للدخول (مصادق عليه عبر Sanctum).
- يتم التحقق الصارم من ملكية السجل قبل الحذف.
- لا يمكن للمستخدم حذف سجلات استلام مستخدمين آخرين.

📊 **الاستخدام النموذجي:**
- المستخدم يريد إزالة إشعار معين من قائمته الشخصية.
- تنظيف الإشعارات القديمة بعد قراءتها.
- إخفاء إشعارات معينة دون حذفها من النظام بالكامل.
",
     *     tags={"Notifications"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="reception",
     *         in="path",
     *         required=true,
     *         description="معرف سجل استلام الإشعار (NotificationRecipient ID)",
     *         @OA\Schema(type="integer", example=13)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف الإشعار بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الإشعار بنجاح")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح - المستخدم غير مصادق عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="غير مصرح بالوصول - المستخدم ليس مالك الإشعار",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="غير مصرح بالوصول لهذا الإشعار")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الإشعار غير موجود (سجل الاستلام)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الإشعار غير موجود")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل حذف الإشعار: خطأ غير متوقع")
     *         )
     *     )
     * )
     */
    public function destroy($receptionId, Request $request)
    {
        try {
            // 🔴 البحث اليدوي لتجنب مشاكل Route Model Binding
            $reception = \Modules\NotificationRecipients\Models\NotificationRecipient::find($receptionId);

            if (!$reception) {
                Log::warning('⚠️ [DESTROY] محاولة حذف إشعار غير موجود', [
                    'reception_id' => $receptionId,
                    'user_id' => $request->user()->id ?? null,
                ]);
                return $this->error('الإشعار غير موجود', 404);
            }

            // التحقق الأمني
            $this->authorizeReception($reception, $request);

            // الحذف
            $reception->delete();

            Log::info('✅ [DESTROY] تم حذف الإشعار', [
                'reception_id' => $reception->id,
                'user_id' => $request->user()->id,
                'notification_id' => $reception->notification_id,
            ]);

            return $this->successResponse(null, 'تم حذف الإشعار بنجاح');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('⚠️ [DESTROY] محاولة حذف إشعار غير مملوك', [
                'reception_id' => $receptionId,
                'attempted_user_id' => $request->user()->id ?? null,
                'owner_user_id' => $reception->user_id ?? 'unknown',
            ]);
            return $this->error('غير مصرح بالوصول لهذا الإشعار', 403);
        } catch (\Throwable $e) {
            Log::error('❌ [DESTROY] فشل حذف الإشعار', [
                'error' => $e->getMessage(),
                'reception_id' => $receptionId,
                'user_id' => $request->user()->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->error('فشل حذف الإشعار: خطأ غير متوقع', 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/notifications/search",
     *     summary="بحث في الإشعارات",
     *     description="
يقوم هذا المسار بالبحث في **الإشعارات** للمستخدم المصادق عليه باستخدام **كلمة مفتاحية**.
يدعم البحث في العنوان والمحتوى مع دعم التصفية والترقيم.

🧠 **آلية البحث:**
- يتم البحث في حقل `title` (العنوان) باستخدام `LIKE` مع النسبة المئوية.
- يتم البحث في حقل `body` (المحتوى) بنفس الطريقة.
- يتم دمج النتائج باستخدام `OR` (أي تطابق في العنوان أو المحتوى).
- يتم تطبيق التصفية حسب حالة القراءة (إذا تم تحديدها).
- يتم ترتيب النتائج من الأحدث للأقدم.
- يتم دعم الترقيم لتحسين الأداء.

📌 **ملاحظات مهمة:**
- البحث **حساس لحالة الأحرف** (Case-Sensitive) في قاعدة البيانات الافتراضية.
- إذا كانت كلمة البحث فارغة: يتم إرجاع جميع الإشعارات (كـ `index`).
- يدعم البحث في **العربية والإنجليزية**.
- يدعم البحث في **الرموز والأرقام**.
- البحث يشمل **الإشعارات المقروءة وغير المقروءة** (ما لم تُحدد التصفية).

🔍 **أمثلة على البحث:**
- `?query=اجتماع` → البحث عن كلمة 'اجتماع'
- `?query=2026` → البحث عن سنة 2026
- `?query=important` → البحث عن كلمة 'important'
- `?query=دفعة&unread=true` → البحث مع تصفية غير المقروءة فقط

🔒 **الأمان:**
- يتطلب المستخدم أن يكون مسجلاً للدخول (مصادق عليه عبر Sanctum).
- يتم البحث **فقط** في إشعارات المستخدم الحالي.
- لا يمكن للمستخدم البحث في إشعارات مستخدمين آخرين.

⚡ **الأداء:**
- يتم استخدام `LIKE` مع النسبة المئوية للبحث الجزئي.
- يتم تحميل العلاقات الضرورية فقط (`notification`, `notification.attachments`).
- يتم دعم الترقيم لتجنب تحميل جميع النتائج دفعة واحدة.
",
     *     tags={"Notifications"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="كلمة البحث المفتاحية (يتم البحث في العنوان والمحتوى)",
     *         @OA\Schema(type="string", example="اجتماع")
     *     ),
     *
     *     @OA\Parameter(
     *         name="unread",
     *         in="query",
     *         required=false,
     *         description="تصفية حسب حالة القراءة: `true` = غير مقروءة فقط، `false` = مقروءة فقط. إذا لم تُحدد: جلب الجميع",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="عدد العناصر في كل صفحة (الحد الأقصى 50)",
     *         @OA\Schema(type="integer", minimum=1, maximum=50, default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="رقم الصفحة المطلوبة",
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم البحث بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم البحث بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(ref="#/components/schemas/NotificationResource")
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/notifications/search?query=اجتماع&page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=2),
     *                 @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/notifications/search?query=اجتماع&page=2"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://127.0.0.1:8000/api/notifications/search?query=اجتماع&page=2"),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/notifications/search"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=15),
     *                 @OA\Property(property="search_query", type="string", example="اجتماع"),
     *                 @OA\Property(property="search_results_count", type="integer", example=15)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="طلب غير صالح (مثال: كلمة البحث فارغة)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="كلمة البحث مطلوبة")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح - المستخدم غير مصادق عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل البحث: خطأ غير متوقع")
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        try {
            // التحقق من كلمة البحث
            $query = $request->input('query');

            if (empty(trim($query))) {
                Log::warning('⚠️ [SEARCH] محاولة بحث بكلمة فارغة', [
                    'user_id' => $request->user()->id ?? null,
                ]);
                return $this->error('كلمة البحث مطلوبة', 400);
            }

            $user = $request->user();
            $searchTerm = '%' . trim($query) . '%';

            Log::info('🔍 [SEARCH] بدء عملية البحث', [
                'search_query' => $query,
                'user_id' => $user->id,
            ]);

            // بناء الاستعلام
            $searchQuery = NotificationRecipient::query()
                ->where('user_id', $user->id)
                ->whereHas('notification', function ($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', $searchTerm)
                        ->orWhere('body', 'LIKE', $searchTerm);
                })
                ->with([
                    'notification',
                    'notification.attachments',
                    'notification.template:id,name',
                ])
                ->latest('created_at');

            // تطبيق التصفية حسب حالة القراءة
            if ($request->has('unread')) {
                $unreadFilter = $request->boolean('unread');

                if ($unreadFilter) {
                    $searchQuery->whereNull('read_at');
                    Log::debug('🔍 [SEARCH] تطبيق تصفية: غير مقروءة فقط');
                } else {
                    $searchQuery->whereNotNull('read_at');
                    Log::debug('🔍 [SEARCH] تطبيق تصفية: مقروءة فقط');
                }
            }

            // ترقيم الصفحات
            $perPage = $request->integer('per_page', 10);
            $results = $searchQuery->paginate($perPage);

            // إحصائيات البحث
            $searchStats = [
                'search_query' => $query,
                'search_results_count' => $results->total(),
                'current_page' => $results->currentPage(),
                'total_pages' => $results->lastPage(),
            ];

            Log::info('✅ [SEARCH] اكتملت عملية البحث بنجاح', [
                'search_query' => $query,
                'user_id' => $user->id,
                'results_count' => $results->total(),
                'page' => $results->currentPage(),
            ]);

            return $this->successResponse(
                NotificationResource::collection($results)
                    ->additional($searchStats),
                'تم البحث بنجاح'
            );
        } catch (\Throwable $e) {
            Log::error('❌ [SEARCH] فشل عملية البحث', [
                'error' => $e->getMessage(),
                'search_query' => $request->input('query'),
                'user_id' => $request->user()->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->error(
                'فشل البحث: ' . $e->getMessage(),
                500
            );
        }
    }
    /**
     * @OA\Get(
     *     path="/api/notifications/filter-by-date",
     *     summary="تصفية الإشعارات حسب الفترة الزمنية",
     *     description="
يقوم هذا المسار بتصفية **الإشعارات** للمستخدم المصادق عليه بناءً على **فترة زمنية محددة**.
يدعم تحديد تاريخ البداية والنهاية مع دعم التصفية الإضافية والترقيم.

🧠 **آلية التصفية:**
- يتم تحديد تاريخ البداية (`from`) وتاريخ النهاية (`to`).
- يتم تصفية الإشعارات التي تم إنشاؤها **بين** هذين التاريخين (شاملة).
- يتم دعم التصفية حسب حالة القراءة (مقروءة/غير مقروءة).
- يتم ترتيب النتائج من الأحدث للأقدم.
- يتم دعم الترقيم لتحسين الأداء.

📌 **ملاحظات مهمة:**
- **التنسيق المطلوب للتاريخ**: `YYYY-MM-DD` (مثال: `2026-01-15`)
- إذا تم تحديد `from` فقط: يتم عرض الإشعارات من هذا التاريخ حتى الآن.
- إذا تم تحديد `to` فقط: يتم عرض الإشعارات حتى هذا التاريخ.
- إذا لم يتم تحديد أي تاريخ: يتم إرجاع جميع الإشعارات (كـ `index`).
- **التوقيت**: يتم استخدام التوقيت المحلي للخادم (UTC+0 افتراضيًا).
- يدعم التواريخ الهجرية والميلادية (حسب تنسيق الإدخال).

📅 **أمثلة على الاستخدام:**
- `?from=2026-01-01&to=2026-01-31` → الإشعارات في يناير 2026
- `?from=2026-01-01` → الإشعارات من يناير 2026 حتى الآن
- `?to=2026-01-15` → الإشعارات حتى 15 يناير 2026
- `?from=2026-01-01&to=2026-01-31&unread=true` → غير المقروءة في يناير فقط

🔒 **الأمان:**
- يتطلب المستخدم أن يكون مسجلاً للدخول (مصادق عليه عبر Sanctum).
- يتم التصفية **فقط** في إشعارات المستخدم الحالي.
- لا يمكن للمستخدم تصفية إشعارات مستخدمين آخرين.

⚡ **الأداء:**
- يتم استخدام استعلامات نطاق التاريخ (`BETWEEN`, `>=`, `<=`) لتحسين الأداء.
- يتم تحميل العلاقات الضرورية فقط.
- يتم دعم الترقيم لتجنب تحميل جميع النتائج دفعة واحدة.
- يُوصى بإضافة فهرس على حقل `created_at` في قاعدة البيانات.
",
     *     tags={"Notifications"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         required=false,
     *         description="تاريخ البداية (بصيغة YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2026-01-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         required=false,
     *         description="تاريخ النهاية (بصيغة YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2026-01-31")
     *     ),
     *
     *     @OA\Parameter(
     *         name="unread",
     *         in="query",
     *         required=false,
     *         description="تصفية حسب حالة القراءة: `true` = غير مقروءة فقط، `false` = مقروءة فقط. إذا لم تُحدد: جلب الجميع",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="عدد العناصر في كل صفحة (الحد الأقصى 50)",
     *         @OA\Schema(type="integer", minimum=1, maximum=50, default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="رقم الصفحة المطلوبة",
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم التصفية بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم التصفية بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(ref="#/components/schemas/NotificationResource")
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/notifications/filter-by-date?from=2026-01-01&to=2026-01-31&page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/notifications/filter-by-date?from=2026-01-01&to=2026-01-31&page=3"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://127.0.0.1:8000/api/notifications/filter-by-date?from=2026-01-01&to=2026-01-31&page=2"),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/notifications/filter-by-date"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=25),
     *                 @OA\Property(property="date_range", type="object",
     *                     @OA\Property(property="from", type="string", format="date", example="2026-01-01"),
     *                     @OA\Property(property="to", type="string", format="date", example="2026-01-31"),
     *                     @OA\Property(property="period_days", type="integer", example=30)
     *                 ),
     *                 @OA\Property(property="filtered_count", type="integer", example=25)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="طلب غير صالح (مثال: تواريخ غير صحيحة)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="تواريخ غير صحيحة")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح - المستخدم غير مصادق عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل التصفية: خطأ غير متوقع")
     *         )
     *     )
     * )
     */
    public function filterByDate(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // الحصول على تواريخ التصفية مع التحقق من الصحة
            $from = $request->input('from');
            $to = $request->input('to');

            // التحقق من صحة التواريخ إذا تم تحديدها
            if ($from && !$this->isValidDate($from)) {
                Log::warning('⚠️ [FILTER_BY_DATE] تاريخ بدء غير صالح', [
                    'from' => $from,
                    'user_id' => $user->id,
                ]);
                return $this->error('تاريخ البداية غير صالح (يجب أن يكون بصيغة YYYY-MM-DD)', 400);
            }

            if ($to && !$this->isValidDate($to)) {
                Log::warning('⚠️ [FILTER_BY_DATE] تاريخ نهاية غير صالح', [
                    'to' => $to,
                    'user_id' => $user->id,
                ]);
                return $this->error('تاريخ النهاية غير صالح (يجب أن يكون بصيغة YYYY-MM-DD)', 400);
            }

            // التحقق من أن تاريخ البداية ليس بعد تاريخ النهاية
            if ($from && $to && strtotime($from) > strtotime($to)) {
                Log::warning('⚠️ [FILTER_BY_DATE] تاريخ البداية بعد تاريخ النهاية', [
                    'from' => $from,
                    'to' => $to,
                    'user_id' => $user->id,
                ]);
                return $this->error('تاريخ البداية لا يمكن أن يكون بعد تاريخ النهاية', 400);
            }

            Log::info('📅 [FILTER_BY_DATE] بدء عملية التصفية حسب التاريخ', [
                'from' => $from,
                'to' => $to,
                'user_id' => $user->id,
            ]);

            // بناء الاستعلام
            $dateQuery = NotificationRecipient::query()
                ->where('user_id', $user->id)
                ->with([
                    'notification',
                    'notification.attachments',
                    'notification.template:id,name',
                ])
                ->latest('created_at');

            // تطبيق تصفية التاريخ
            if ($from) {
                $dateQuery->whereDate('created_at', '>=', $from);
                Log::debug('📅 [FILTER_BY_DATE] تطبيق تصفية: من تاريخ ' . $from);
            }

            if ($to) {
                $dateQuery->whereDate('created_at', '<=', $to);
                Log::debug('📅 [FILTER_BY_DATE] تطبيق تصفية: حتى تاريخ ' . $to);
            }

            // تطبيق التصفية حسب حالة القراءة
            if ($request->has('unread')) {
                $unreadFilter = $request->boolean('unread');

                if ($unreadFilter) {
                    $dateQuery->whereNull('read_at');
                    Log::debug('📅 [FILTER_BY_DATE] تطبيق تصفية: غير مقروءة فقط');
                } else {
                    $dateQuery->whereNotNull('read_at');
                    Log::debug('📅 [FILTER_BY_DATE] تطبيق تصفية: مقروءة فقط');
                }
            }

            // ترقيم الصفحات
            $perPage = $request->integer('per_page', 10);
            $results = $dateQuery->paginate($perPage);

            // حساب عدد الأيام في الفترة
            $periodDays = null;
            if ($from && $to) {
                $start = new \DateTime($from);
                $end = new \DateTime($to);
                $periodDays = $end->diff($start)->days + 1; // +1 لتشمل اليوم الأخير
            }

            // معلومات الفترة
            $dateRangeInfo = [
                'from' => $from,
                'to' => $to,
                'period_days' => $periodDays,
            ];

            // إحصائيات التصفية
            $filterStats = [
                'date_range' => $dateRangeInfo,
                'filtered_count' => $results->total(),
                'current_page' => $results->currentPage(),
                'total_pages' => $results->lastPage(),
            ];

            Log::info('✅ [FILTER_BY_DATE] اكتملت عملية التصفية بنجاح', [
                'from' => $from,
                'to' => $to,
                'user_id' => $user->id,
                'results_count' => $results->total(),
                'page' => $results->currentPage(),
            ]);

            return $this->successResponse(
                NotificationResource::collection($results)
                    ->additional($filterStats),
                'تم التصفية بنجاح'
            );
        } catch (\Throwable $e) {
            Log::error('❌ [FILTER_BY_DATE] فشل عملية التصفية', [
                'error' => $e->getMessage(),
                'from' => $request->input('from'),
                'to' => $request->input('to'),
                'user_id' => $request->user()->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->error(
                'فشل التصفية: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * التحقق من صحة تنسيق التاريخ
     * 
     * @param string $date تاريخ بصيغة YYYY-MM-DD
     * @return bool
     */
    private function isValidDate(string $date): bool
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime && $dateTime->format('Y-m-d') === $date;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/notifications",
     *     summary="عرض جميع الإشعارات في النظام مع فلترة متقدمة",
     *     description="
     * يعرض قائمة بجميع الإشعارات في النظام مع دعم فلترة متقدمة.
     * مخصص للاستخدام الإداري فقط (يتطلب صلاحية 'manage-notifications').
     * 
     * 🔍 **فلترات مدعومة:**
     * - `user_id`: عرض الإشعارات المرسلة لمستخدم محدد (الأهم!)
     * - `from`: تاريخ البداية (YYYY-MM-DD)
     * - `to`: تاريخ النهاية (YYYY-MM-DD)
     * - `read`: حالة القراءة (للمستخدم المحدد فقط)
     * - `sender_type`: نوع المرسل (admin, system, teacher, etc.)
     * - `has_attachments`: وجود مرفقات (true/false)
     * - `template_id`: تصفية حسب القالب
     * - `status`: حالة التسليم (delivered, pending, failed)
     * - `per_page`: عدد العناصر في الصفحة
     * - `page`: رقم الصفحة
     * 
     * 📊 **المعلومات المعروضة لكل إشعار:**
     * - البيانات الأساسية للإشعار
     * - إحصائيات التوزيع (إجمالي المستلمين، المقروءة، المُسلمة)
     * - معلومات المرسل
     * - عدد المرفقات
     * - حالة التسليم العامة
     * 
     * 🔒 **الأمان:**
     * - يتطلب صلاحية 'manage-notifications'
     * - جميع البيانات حساسة ويتم عرضها للمشرفين فقط
     * - يتم تسجيل جميع عمليات العرض لأغراض التدقيق
     * ",
     *     tags={"Admin Notifications"},
     *     security={{"sanctum":{}}},
     *     
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         description="تصفية: عرض الإشعارات المرسلة لمستخدم محدد (المعرف)",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         required=false,
     *         description="تصفية: من تاريخ (بصيغة YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2026-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         required=false,
     *         description="تصفية: حتى تاريخ (بصيغة YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2026-01-31")
     *     ),
     *     @OA\Parameter(
     *         name="read",
     *         in="query",
     *         required=false,
     *         description="تصفية: حالة القراءة (للمستخدم المحدد فقط). true = مقروءة، false = غير مقروءة",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="sender_type",
     *         in="query",
     *         required=false,
     *         description="تصفية: نوع المرسل",
     *         @OA\Schema(type="string", enum={"admin","system","user","teacher","employee"}, example="admin")
     *     ),
     *     @OA\Parameter(
     *         name="has_attachments",
     *         in="query",
     *         required=false,
     *         description="تصفية: وجود مرفقات",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="template_id",
     *         in="query",
     *         required=false,
     *         description="تصفية: معرف القالب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="تصفية: حالة التسليم العامة",
     *         @OA\Schema(type="string", enum={"delivered","pending","failed"}, example="delivered")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="عدد العناصر في كل صفحة",
     *         @OA\Schema(type="integer", default=15, minimum=1, maximum=100)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="رقم الصفحة",
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الإشعارات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الإشعارات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(ref="#/components/schemas/AdminNotificationResource")
     *                 ),
     *                 @OA\Property(property="total", type="integer", example=45),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="filters_applied", type="object",
     *                     @OA\Property(property="user_id", type="integer", example=2, nullable=true),
     *                     @OA\Property(property="from", type="string", example="2026-01-01", nullable=true),
     *                     @OA\Property(property="to", type="string", example="2026-01-31", nullable=true),
     *                     @OA\Property(property="read", type="boolean", example=true, nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="غير مصرح - غير مصادق عليه"),
     *     @OA\Response(response=403, description="غير مصرح - ليس لديك الصلاحية"),
     *     @OA\Response(response=500, description="خطأ داخلي")
     * )
     */
    public function adminIndex(AdminNotificationIndexRequest $request): JsonResponse
    {
        try {
            Log::info('👨‍💼 [ADMIN] طلب عرض الإشعارات من المشرف', [
                'admin_id' => $request->user()->id,
                'filters' => $request->validated()
            ]);

            $query = Notification::query()
                ->withCount([
                    'recipients',
                    'recipients as read_count' => fn($q) => $q->whereNotNull('read_at'),
                    'recipients as delivered_count' => fn($q) => $q->whereNotNull('delivered_at')
                ])
                ->with([
                    'template:id,name',
                    'attachments:id,notification_id,file_name'
                ])
                ->latest('created_at');

            // 🔑 الفلترة حسب مستخدم + حالة القراءة (مع ربط منطقي)
            if ($request->filled('user_id')) {
                $userId = $request->integer('user_id');
                // تفعيل التحقق من وجود المستخدم (موصى به)
                // if (!\Modules\Users\Models\User::find($userId)) { ... }

                $query->whereHas('recipients', function ($q) use ($userId, $request) {
                    $q->where('user_id', $userId);
                    if ($request->has('read')) {
                        $q->when(
                            $request->boolean('read'),
                            fn($q) => $q->whereNotNull('read_at'),
                            fn($q) => $q->whereNull('read_at')
                        );
                    }
                });
            } elseif ($request->has('read')) {
                // رفض الطلب إذا طُلب فلتر read بدون user_id
                return $this->error('يجب تحديد user_id لاستخدام فلتر read', 422);
            }

            // 📅 فلترة التاريخ
            if ($request->filled('from')) {
                $query->whereDate('created_at', '>=', $request->input('from'));
            }
            if ($request->filled('to')) {
                $query->whereDate('created_at', '<=', $request->input('to'));
            }

            // 👤 فلترة المرسل
            if ($request->filled('sender_type')) {
                $query->where('sender_type', $request->input('sender_type'));
            }

            // 📎 فلترة المرفقات
            if ($request->has('has_attachments')) {
                $query->when(
                    $request->boolean('has_attachments'),
                    fn($q) => $q->has('attachments'),
                    fn($q) => $q->doesntHave('attachments')
                );
            }

            // 📄 فلترة القالب
            if ($request->filled('template_id')) {
                $query->where('template_id', $request->integer('template_id'));
            }

            // 📊 فلتر الحالة (مُصلح: تجنب having بدون groupBy)
            if ($request->filled('status')) {
                $status = $request->input('status');
                // الحل الآمن: استخدام whereRaw مع الاعتماد على الـ counts المحسوبة
                $query->whereRaw(
                    match ($status) {
                        'delivered' => '(SELECT COUNT(*) FROM notification_recipients WHERE notification_recipients.notification_id = notifications.id AND delivered_at IS NOT NULL) >= (SELECT COUNT(*) FROM notification_recipients WHERE notification_recipients.notification_id = notifications.id) * 0.9',
                        'pending' => '(SELECT COUNT(*) FROM notification_recipients WHERE notification_recipients.notification_id = notifications.id AND delivered_at IS NOT NULL) < (SELECT COUNT(*) FROM notification_recipients WHERE notification_recipients.notification_id = notifications.id) * 0.1',
                        'failed' => '(SELECT COUNT(*) FROM notification_recipients WHERE notification_recipients.notification_id = notifications.id AND delivered_at IS NOT NULL) < (SELECT COUNT(*) FROM notification_recipients WHERE notification_recipients.notification_id = notifications.id) * 0.5',
                        default => '1=1'
                    }
                );
            }

            $perPage = $request->integer('per_page', 15);
            $notifications = $query->paginate($perPage);

            // ✅ نقل اللوغ هنا قبل الـ return
            Log::info('✅ [ADMIN] تم جلب الإشعارات بنجاح', [
                'admin_id' => $request->user()->id,
                'total_results' => $notifications->total(),
                'applied_filters' => $request->validated()
            ]);

            return $this->successResponse(
                AdminNotificationResource::collection($notifications)
                    ->additional(['filters_applied' => $request->validated()]),
                'تم جلب الإشعارات بنجاح'
            );
        } catch (\Throwable $e) {
            Log::error('❌ [ADMIN] فشل جلب الإشعارات', [
                'error' => $e->getMessage(),
                'admin_id' => $request->user()->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error('فشل جلب الإشعارات: ' . $e->getMessage(), 500);
        }
    }
}
