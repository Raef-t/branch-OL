<?php

namespace Modules\Enrollments\Http\Controllers;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Modules\Enrollments\Services\QrEncryptionService;
use Modules\Students\Models\Student;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="QR Code",
 *     description="إدارة رموز الاستجابة السريعة (QR Codes) الخاصة بالطلاب لأغراض الدخول أو التحقق"
 * )
 */
class QrCodeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/enrollments/qr-code",
     *     summary="توليد رمز QR لطالب مصادق عليه",
     *     description="يُولّد رمز QR بصيغة SVG يحتوي على معرف طالب مشفر. يتطلب أن يكون المستخدم مصادقًا ومرتبطًا بسجل طالب.",
     *     operationId="generateQrCode",
     *     security={{"sanctum":{}}},
     *     tags={"QR Code"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم توليد رمز QR بنجاح",
     *         @OA\MediaType(
     *             mediaType="image/svg+xml",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary",
     *                 example="<svg>...</svg>"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول (المستخدم غير مصادق عليه)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="المستخدم ليس طالبًا أو لا يملك سجل طالب",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to access this resource.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء توليد رمز QR")
     *         )
     *     )
     * )
     */
    public function generate(Request $request): BaseResponse
    {
        $user = $request->user();

        // التحقق من المصادقة
        // if (!$user) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'المستخدم غير مصادق عليه.'
        //     ], 401);
        // }

        // التحقق من وجود علاقة طالب
        // if (!$user->student) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'المستخدم ليس طالبًا.'
        //     ], 403);
        // }

        $studentId = $user->student->id;

        // تشفير المعرف
        $encryptedId = QrEncryptionService::encryptStudentId($studentId);

        // ✅ تحويل إلى Base64URL آمن للاستخدام في QR
        $encryptedId = str_replace(['+', '/', '='], ['-', '_', ''], $encryptedId);

        $qrContent = "STUDENT:{$encryptedId}";

        // توليد QR بصيغة SVG
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrImage = $writer->writeString($qrContent);

        return response($qrImage, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'private, max-age=3600');
    }

    /**
     * @OA\Post(
     *     path="/api/enrollments/scan-qr",
     *     summary="فحص رمز QR واستخراج بيانات الطالب",
     *     description="يتم إرسال محتوى رمز QR (النص المستخرج من الصورة) إلى هذا الـ endpoint لفك تشفيره والتحقق من صحة الطالب المرتبط به.",
     *     operationId="scanQrCode",
     *     security={{"sanctum":{}}},
     *     tags={"QR Code"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="محتوى رمز QR بعد مسحه (يجب أن يبدأ بـ 'STUDENT:')",
     *         @OA\JsonContent(
     *             required={"qr_content"},
     *             @OA\Property(
     *                 property="qr_content",
     *                 type="string",
     *                 example="STUDENT:KH5UwZP-Z_FxRSzA2f0N-g",
     *                 description="النص الكامل المستخرج من رمز QR (باستخدام Base64URL)"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم فك تشفير الرمز وتم العثور على الطالب",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم فك تشفير رمز QR بنجاح"),
     *             @OA\Property(
     *                 property="student",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="خالد"),
     *                 @OA\Property(property="last_name", type="string", example="أحمد"),
     *                 @OA\Property(property="full_name", type="string", example="خالد أحمد"),
     *                 @OA\Property(property="branch_id", type="integer", example=2),
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="رمز QR غير صالح أو الطالب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="رمز QR غير صالح أو الطالب غير موجود.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="بيانات الإدخال غير صالحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="رمز QR غير صالح أو التنسيق غير صحيح."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="qr_content",
     *                     type="array",
     *                     @OA\Items(type="string", example="رمز QR غير صالح أو التنسيق غير صحيح.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function scanQr(Request $request)
    {
        // 1. التحقق من أن المستخدم مصادق عليه
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // 2. التحقق من أن دور المستخدم هو "admin" (كما هو مخزن في قاعدة البيانات)
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن للمستخدمين غير الإداريين الوصول إلى هذه الميزة.'
            ], 403);
        }

        // 3. باقي منطق فك تشفير QR
        $qrContent = $request->input('qr_content');

        if (!$qrContent || !str_starts_with($qrContent, 'STUDENT:')) {
            throw ValidationException::withMessages([
                'qr_content' => 'رمز QR غير صالح أو التنسيق غير صحيح.',
            ]);
        }

        $encryptedPart = substr($qrContent, strlen('STUDENT:'));

        try {
            // عكس Base64URL إلى Base64 عادي
            $base64 = str_replace(['-', '_'], ['+', '/'], $encryptedPart);
            $padding = strlen($base64) % 4;
            if ($padding) {
                $base64 .= str_repeat('=', 4 - $padding);
            }

            $studentId = QrEncryptionService::decryptStudentId($base64);
            $student = Student::findOrFail($studentId);

            return response()->json([
                'success' => true,
                'message' => 'تم فك تشفير رمز QR بنجاح',
                'student' => [
                    'id' => $student->id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'full_name' => "{$student->first_name} {$student->last_name}",
                    'branch_id' => $student->branch_id,
                    'institute_branch_id' => $student->institute_branch_id,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'رمز QR غير صالح أو الطالب غير موجود.',
            ], 400);
        }
    }
}
