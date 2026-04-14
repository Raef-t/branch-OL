<?php

namespace Modules\Attendances\Services;

use Modules\Attendances\Models\Attendance;
use Modules\Students\Models\Student;
use Modules\Students\Models\BatchStudent;
use Illuminate\Support\Collection;

class AttendanceService
{
    public function createBulk(array $studentsIds, array $data): Collection
    {
        $created = collect();

        foreach ($studentsIds as $studentId) {

            // 🔍 جلب الطالب
            $student = Student::find($studentId);
            if (! $student) {
                continue;
            }

            // 🟦 التأكد من وجود دفعة للطالب
            $batch = $student->latestBatchStudent?->batch_id;
            if (! $batch) {
                continue;
            }

            // ❌ التأكد من عدم وجود تسجيل سابق في اليوم نفسه
            $exists = Attendance::where('student_id', $student->id)
                ->where('attendance_date', $data['attendance_date'])
                ->exists();

            if ($exists) {
                continue;
            }

            // 🔹 إنشاء سجل الحضور
            $attendance = Attendance::create([
                'institute_branch_id' => $student->institute_branch_id,
                'student_id'          => $student->id,
                'batch_id'            => $batch,
                'attendance_date'     => $data['attendance_date'],
                'status'              => $data['status'],
                'recorded_by'         => $data['recorded_by'],
                'recorded_at'         => $data['recorded_at'],
                'note'                => $data['note'] ?? null,
                'device_id'           => null,
            ]);

            $created->push($attendance);
        }

        return $created;
    }
}
