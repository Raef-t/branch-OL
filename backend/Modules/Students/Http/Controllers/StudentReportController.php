<?php

namespace Modules\Students\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Students\Models\Student;
use Modules\Students\Services\StudentReportService;
use Illuminate\Support\Facades\Response;

class StudentReportController extends Controller
{
    protected StudentReportService $reportService;

    public function __construct(StudentReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * توليد تقرير تسجيل طالب وإرجاع رابط التحميل
     *
     * GET /api/students/{id}/report
     */
    public function generate(int $id)
    {
        $student = Student::findOrFail($id);

        $reportPath = $this->reportService->generateEnrollmentReport($id);

        return response()->json([
            'message' => 'تم إنشاء التقرير بنجاح',
            'report_url' => url("storage/{$reportPath}")
        ]);
    }

    /**
     * تنزيل التقرير مباشرة
     *
     * GET /api/students/{id}/report/download
     */
    /**
     * @OA\Get(
     *     path="/api/students/{id}/report/download",
     *     summary="تحميل تقرير تسجيل الطالب",
     *     description="يعطيك ملف التقرير بصيغة DOCX للطالب حسب الـ ID",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="معرّف الطالب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ملف التقرير جاهز للتحميل",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الملف غير موجود"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function download(int $id)
    {
        $student = Student::findOrFail($id);

        $reportPath = $this->reportService->generateEnrollmentReport($id);
        $fullPath = storage_path("app/{$reportPath}");

        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'الملف غير موجود'], 404);
        }

        return Response::download($fullPath, "student_report_{$student->id}.docx");
    }
}
