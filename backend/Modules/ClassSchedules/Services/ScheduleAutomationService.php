<?php

namespace Modules\ClassSchedules\Services;

use Modules\Batches\Models\Batch;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\Instructors\Models\Instructor;
use Modules\ClassSchedules\Models\ClassScheduleDraft;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ScheduleAutomationService
{
    protected string $pythonPath;
    protected string $scriptPath;

    public function __construct()
    {
        $this->pythonPath = config('services.scheduler.python_path') ?? base_path('../scheduler_lab/venv/Scripts/python.exe');
        $this->scriptPath = config('services.scheduler.script_path') ?? base_path('../scheduler_lab/scheduler_api.py');
    }

    /**
     * الحطوة 1: توليد ملف JSON للمدخلات من قاعدة البيانات
     */
    public function generateInputJson(array $batchIds): string
    {
        DB::reconnect();
        
        $data = [
            "days" => ["sat", "sun", "mon", "tue", "wed", "thu", "fri"],
            "slots" => $this->getSlotsConfig(),
            "branches" => [],
            "teachers" => [],
            "rooms" => [],
        ];

        // 1. جلب الشعب الدراسية والمواد المرتبطة بها
        Log::info("Fetching batches for IDs: " . implode(', ', $batchIds));
        $batches = Batch::whereIn('id', $batchIds)->with(['batchSubjects.subject', 'batchSubjects.instructorSubject.instructor'])->get();
        Log::info("Fetched " . $batches->count() . " batches.");
        
        foreach ($batches as $batch) {
            $branchData = [
                "id" => $batch->id,
                "name" => $batch->name,
                "classes" => [
                    [
                        "id" => $batch->id, // نستخدم ID الشعبة كـ Class ID لتبسيط الربط
                        "name" => $batch->name,
                        "room_id" => $batch->class_room_id, // ⭐ إضافة الـ ID المفقود
                        "subjects" => []
                    ]
                ]
            ];

            foreach ($batch->batchSubjects as $bs) {
                if ($bs->weekly_lessons <= 0) continue;

                $instructorId = $bs->instructorSubject?->instructor_id;
                
                $branchData["classes"][0]["subjects"][] = [
                    "id" => $bs->id, // نستخدم ID الـ batch_subject_id للربط العكسي بسهولة
                    "name" => $bs->subject->name ?? 'Unknown',
                    "lessons_per_week" => $bs->weekly_lessons,
                    "teacher_ids" => $instructorId ? [$instructorId] : [],
                    "allow_same_subject_same_day" => false // افتراضي
                ];

                // إضافة المدرس لقائمة المدرسين إذا لم يكن موجوداً
                if ($instructorId && !collect($data['teachers'])->contains('id', $instructorId)) {
                    $instructor = $bs->instructorSubject->instructor;
                    $prefs = $instructor->preferences ?? [];
                    
                    $data['teachers'][] = [
                        "id" => $instructorId,
                        "name" => $instructor->name,
                        "priority_level" => (int) ($prefs['priority_level'] ?? 2), // Default to 2
                        "preferences" => [
                            "blocked_slots"  => $prefs['blocked_slots'] ?? [],
                            "preferred_days" => $prefs['preferred_days'] ?? [],
                            "avoid_days"     => $prefs['avoid_days'] ?? [],
                            "preferred_slots"=> $prefs['preferred_slots'] ?? [],
                            "avoid_slots"    => $prefs['avoid_slots'] ?? [],
                        ],
                    ];
                }
            }
            $data['branches'][] = $branchData;
        }

        // 2. جلب القاعات
        $rooms = ClassRoom::all();
        foreach ($rooms as $room) {
            $data['rooms'][] = [
                "id" => $room->id,
                "name" => $room->name,
                "capacity" => $room->capacity ?? 30
            ];
        }

        $tempPath = storage_path('app/temp_scheduler_input.json');
        file_put_contents($tempPath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        return $tempPath;
    }

    /**
     * الخطوة 2: تشغيل الباحث (Solver)
     */
    public function runSolver(string $inputPath, array $config = []): array
    {
        $mode = config('services.scheduler.mode', 'local');
        
        // 🚀 Auto-detection fallback: If we are on Linux but mode is 'local', force 'docker'
        // because we cannot run Windows .exe on Linux.
        if ($mode === 'local' && DIRECTORY_SEPARATOR === '/') {
            Log::warning("⚠️ OS is Linux but Scheduler Mode is 'local'. Forcing 'docker' mode for compatibility.");
            $mode = 'docker';
        }

        Log::info("🎯 Scheduler Mode identified: {$mode}");

        if ($mode === 'docker') {
            return $this->runSolverViaHttp($inputPath, $config);
        }

        return $this->runSolverViaShell($inputPath, $config);
    }



    /**
     * تشغيل الباحث عبر HTTP (لبيئة Docker)
     */
    protected function runSolverViaHttp(string $inputPath, array $config): array
    {
        $baseUrl = config('services.scheduler.url', 'http://scheduler:8000');
        $url = rtrim($baseUrl, '/') . '/run';
        
        $inputData = json_decode(file_get_contents($inputPath), true);

        try {
            Log::info("🚀 Sending request to Scheduler API: {$url}");
            
            $startTime = microtime(true);
            $response = Http::timeout(150)
                ->post($url, [
                    'data' => $inputData,
                    'config' => $config
                ]);
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            Log::info("📥 Scheduler API responded in {$duration}s. Status: " . $response->status());

            if ($response->failed()) {
                $errorBody = $response->body();
                Log::error("❌ Scheduler API failed with status " . $response->status() . ": " . $errorBody);
                
                // حاول فك ترميز الخطأ إذا كان JSON
                $errorJson = $response->json();
                $detail = $errorJson['detail'] ?? ($errorJson['message'] ?? 'Unknown API error');
                
                throw new \Exception("فشل الاتصال بنظام الجدولة الذكي (API Error: {$detail})");
            }

            return $response->json();

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("🌐 Connection Failed to Scheduler: " . $e->getMessage());
            throw new \Exception("فشل الاتصال بنظام الجدولة: تأكد من أن حاوية 'scheduler' تعمل وأنها على نفس الشبكة (Network).");
        } catch (\Exception $e) {
            Log::error("🚨 Error calling Scheduler API: " . $e->getMessage());
            throw new \Exception("فشل نظام الجدولة الذكي: " . $e->getMessage());
        }
    }


    /**
     * تشغيل الباحث عبر Shell (للبيئة المحلية)
     */
    protected function runSolverViaShell(string $inputPath, array $config): array
    {
        $configPath = null;
        $command = [$this->pythonPath, $this->scriptPath, '--input', $inputPath];

        if (!empty($config)) {
            $configPath = storage_path('app/temp_scheduler_config.json');
            file_put_contents($configPath, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $command[] = '--config';
            $command[] = $configPath;
            Log::info("📋 Config JSON generated at: {$configPath}");
        }

        try {
            Log::info("💻 Running Shell Command: " . implode(' ', $command));
            
            $process = new Process($command);
            $process->setTimeout(300);
            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                Log::error("Scheduler shell execution failed: " . $errorOutput);
                throw new \Exception("فشل تشغيل السكربت: " . $errorOutput);
            }

            $output = $process->getOutput();
            $result = json_decode($output, true);

            if (!$result || !isset($result['success']) || !$result['success']) {
                $errorMessage = $result['message'] ?? 'Unknown Error';
                Log::error("Scheduler logic error: " . $errorMessage);
                throw new \Exception("فشل نظام الجدولة الذكي: " . $errorMessage);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("Scheduler Exception: " . $e->getMessage());
            throw new \Exception("فشل نظام الجدولة الذكي: " . $e->getMessage());
        } finally {
            if ($configPath && file_exists($configPath)) {
                unlink($configPath);
            }
        }
    }


    /**
     * الخطوة 3: حفظ النتائج في جدول المسودات
     */
    public function saveToDraft(array $solverOutput, string $draftGroupId): void
    {
        // تنظيف أي مسودة سابقة بنفس الاسم (اختياري)
        ClassScheduleDraft::where('draft_group_id', $draftGroupId)->delete();

        $schedule = $solverOutput['schedule'];
        $unassigned = $solverOutput['unassigned'];

        foreach ($schedule as $slot) {
            $batchSubject = BatchSubject::with('batch')->find($slot['subject_id']);
            $roomId = $batchSubject?->batch?->class_room_id ?? $slot['class_id']; // Use batch's room if available

            ClassScheduleDraft::create([
                'draft_group_id'   => $draftGroupId,
                'batch_subject_id' => $slot['subject_id'],
                'day_of_week'      => $this->mapDay($slot['day']),
                'period_number'    => $slot['slot_id'],
                'class_room_id'    => $roomId,
                'is_conflict'      => false,
            ]);
        }

        // معالجة الحصص المعلقة كتعارضات
        foreach ($unassigned as $u) {
            for ($i = 0; $i < $u['count']; $i++) {
                ClassScheduleDraft::create([
                    'draft_group_id'   => $draftGroupId,
                    'batch_subject_id' => $u['subject_id'],
                    'is_conflict'      => true,
                    'conflict_message' => 'لم يجد النظام وقاً متاحاً لهذه الحصة',
                ]);
            }
        }
    }

    protected function getSlotsConfig(): array
    {
        // هذا مجرد مثال، يفضل جلبها من إعدادات النظام
        return [
            ["id" => 1, "name" => "الحصة 1"],
            ["id" => 2, "name" => "الحصة 2"],
            ["id" => 3, "name" => "الحصة 3"],
            ["id" => 4, "name" => "الحصة 4"],
            ["id" => 5, "name" => "الحصة 5"],
        ];
    }

    protected function mapDay(string $dayAbbr): string
    {
        $map = [
            "sat" => "saturday",
            "sun" => "sunday",
            "mon" => "monday",
            "tue" => "tuesday",
            "wed" => "wednesday",
            "thu" => "thursday",
            "fri" => "friday",
        ];
        return $map[$dayAbbr] ?? $dayAbbr;
    }
}
