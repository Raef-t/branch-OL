<?php

namespace Modules\ClassSchedules\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TestScheduler extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scheduler:test {batch_id*}';

    protected $description = 'اختبار نظام الجدولة الذكي لشعبة أو أكثر';

    public function handle(\Modules\ClassSchedules\Services\ScheduleAutomationService $service)
    {
        $batchIds = $this->argument('batch_id');
        $this->info("🚀 بدء اختبار الجدولة للشعب: " . implode(', ', $batchIds));

        try {
            $this->comment("1. توليد ملف المدخلات...");
            $inputPath = $service->generateInputJson($batchIds);
            $this->info("✅ تم توليد الملف في: $inputPath");

            $this->comment("2. تشغيل الخوارزمية (بايثون)...");
            $output = $service->runSolver($inputPath);
            $this->info("✅ تم العثور على حل! (عدد الحصص المجدولة: " . count($output['schedule']) . ")");

            $this->comment("3. حفظ النتائج في المسودة...");
            $draftId = "test_draft_" . date('Ymd_His');
            $service->saveToDraft($output, $draftId);
            $this->info("✅ تم الحفظ بنجاح في المسودة بـ ID: $draftId");
            
            $this->info("\n🎉 تمت العملية بنجاح! يمكنك الآن مراجعة الجدول في قاعدة البيانات.");
        } catch (\Exception $e) {
            $this->error("❌ فشل الاختبار: " . $e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
