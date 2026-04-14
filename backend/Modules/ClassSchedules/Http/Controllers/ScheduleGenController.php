<?php

namespace Modules\ClassSchedules\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Modules\Batches\Models\Batch;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\Instructors\Models\Instructor;
use Modules\ClassSchedules\Services\ScheduleAutomationService;

class ScheduleGenController extends Controller
{
    /**
     * جلب بيانات الإعداد للمعالج (Wizard)
     * - الدفعات النشطة مع موادها ومدرسيها
     * - القاعات المتاحة
     * - عدد الحصص اليومية
     */
    public function getSetupData()
    {
        try {
            // 1. جلب الشعب النشطة مع المواد والمدرسين
            $batches = Batch::with([
                'batchSubjects.subject',
                'batchSubjects.instructorSubject.instructor',
                'classRoom',
                'academicBranch',
            ])
            ->where('is_archived', false)
            ->where('is_hidden', false)
            ->get()
            ->map(function ($batch) {
                return [
                    'id'       => $batch->id,
                    'name'     => $batch->name,
                    'branch'   => $batch->academicBranch?->name,
                    'room'     => $batch->classRoom?->name,
                    'room_id'  => $batch->class_room_id,
                    'subjects' => $batch->batchSubjects->map(function ($bs) {
                        return [
                            'id'              => $bs->id,
                            'name'            => $bs->subject?->name,
                            'weekly_lessons'  => $bs->weekly_lessons ?? 0,
                            'instructor_name' => $bs->instructorSubject?->instructor?->name ?? 'غير محدد',
                            'instructor_id'   => $bs->instructorSubject?->instructor_id,
                        ];
                    }),
                ];
            });

            // 2. القاعات
            $rooms = ClassRoom::all()->map(function ($room) {
                return [
                    'id'       => $room->id,
                    'name'     => $room->name,
                    'capacity' => $room->capacity ?? 30,
                ];
            });

            // 3. الحصص اليومية (إعدادات ثابتة حالياً)
            $slots = [
                ['id' => 1, 'name' => 'الحصة 1', 'time' => '08:00 - 08:45'],
                ['id' => 2, 'name' => 'الحصة 2', 'time' => '08:50 - 09:35'],
                ['id' => 3, 'name' => 'الحصة 3', 'time' => '09:40 - 10:25'],
                ['id' => 4, 'name' => 'الحصة 4', 'time' => '10:30 - 11:15'],
                ['id' => 5, 'name' => 'الحصة 5', 'time' => '11:20 - 12:05'],
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'batches' => $batches,
                    'rooms'   => $rooms,
                    'slots'   => $slots,
                    'days'    => ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس'],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('ScheduleGen getSetupData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات الإعداد: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * بدء عملية التوليد الذكي
     */
    public function startGeneration(Request $request)
    {
        $request->validate([
            'batch_ids'   => 'required|array|min:1',
            'batch_ids.*' => 'integer|exists:batches,id',
            'config'      => 'nullable|array',
        ]);

        $batchIds = $request->input('batch_ids');
        $config   = $request->input('config', []);
        $draftGroupId = 'draft_' . now()->format('Ymd_His') . '_' . Str::random(6);

        try {
            Log::info("🚀 Starting schedule generation for batches: " . implode(', ', $batchIds));

            $service = new ScheduleAutomationService();

            // 1. توليد ملف المدخلات
            $inputPath = $service->generateInputJson($batchIds);
            Log::info("✅ Input JSON generated at: {$inputPath}");

            // 2. تشغيل المحلل
            $solverResult = $service->runSolver($inputPath, $config);
            $allSolutions = $solverResult['all_solutions'] ?? [$solverResult['data']];
            Log::info("✅ Solver completed. Solutions found: " . count($allSolutions));

            // 3. حفظ كل حل كمسودة منفصلة
            $drafts = [];
            foreach ($allSolutions as $index => $solution) {
                $solDraftId = $draftGroupId . '_sol' . ($index + 1);
                $solverData = [
                    'schedule'   => $solution['schedule'],
                    'unassigned' => $solution['unassigned'] ?? [],
                ];
                $service->saveToDraft($solverData, $solDraftId);
                
                $drafts[] = [
                    'draft_group_id'  => $solDraftId,
                    'solution_number' => $index + 1,
                    'total_lessons'   => $solution['total_lessons'] ?? count($solution['schedule']),
                    'unassigned'      => $solution['total_unassigned'] ?? count($solution['unassigned'] ?? []),
                    'objective_value' => $solution['objective_value'] ?? 0,
                ];
                Log::info("✅ Draft saved: {$solDraftId}");
            }

            // 4. تنظيف
            if (file_exists($inputPath)) {
                unlink($inputPath);
            }

            return response()->json([
                'success'         => true,
                'message'         => 'تم توليد الجدول بنجاح!',
                'total_solutions' => count($drafts),
                'drafts'          => $drafts,
                'batches_count'   => count($batchIds),
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Generation failed: " . $e->getMessage());
            
            // تحقق إذا كان الخطأ متعلق بالاتصال بالـ Scheduler
            $isConnectionError = str_contains($e->getMessage(), 'فشل الاتصال') || str_contains($e->getMessage(), 'Connection');
            
            return response()->json([
                'success' => false,
                'message' => 'فشل في توليد الجدول: ' . $e->getMessage(),
                'error_type' => $isConnectionError ? 'SCHEDULER_CONNECTION_ERROR' : 'LOGIC_ERROR',
                'suggestion' => $isConnectionError ? 'تأكد من تشغيل حاويات Docker بشكل صحيح.' : 'تحقق من بيانات المدخلات.',
                'debug' => [
                    'mode' => config('services.scheduler.mode'),
                    'url'  => config('services.scheduler.url'),
                ]
            ], 500);
        }

    }
}
