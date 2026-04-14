<?php

namespace Modules\StudentExits\Services;

use Illuminate\Support\Facades\DB;
use Modules\StudentExits\Models\StudentExitLog;

class StudentExitLogService
{
    /**
     * إنشاء سجل خروج فردي
     */
    public function create(array $data): StudentExitLog
    {
        return DB::transaction(function () use ($data) {
            return StudentExitLog::create($data);
        });
    }

    /**
     * إنشاء سجلات خروج جماعية
     * مثال: تسجيل خروج لشعبة كاملة مرة واحدة
     */
    public function createBulk(array $studentsIds, array $data): array
    {
        $created = [];

        DB::transaction(function () use (&$created, $studentsIds, $data) {
            foreach ($studentsIds as $studentId) {
                $entry = StudentExitLog::create([
                    'student_id'  => $studentId,
                    'exit_date'   => $data['exit_date'],
                    'exit_time'   => $data['exit_time'],
                    'return_time' => $data['return_time'] ?? null,
                    'exit_type'   => $data['exit_type'] ?? null,
                    'reason'      => $data['reason'] ?? null,
                    'note'        => $data['note'] ?? null,
                    'recorded_by' => $data['recorded_by'],
                ]);

                $created[] = $entry;
            }
        });

        return $created;
    }

    /**
     * تعديل سجل
     */
    public function update(StudentExitLog $log, array $data): StudentExitLog
    {
        return DB::transaction(function () use ($log, $data) {
            $log->update($data);
            return $log;
        });
    }

    /**
     * حذف سجل
     */
    public function delete(StudentExitLog $log): void
    {
        DB::transaction(function () use ($log) {
            $log->delete();
        });
    }
}
