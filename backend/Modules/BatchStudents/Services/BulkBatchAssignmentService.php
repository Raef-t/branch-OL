<?php

namespace Modules\BatchStudents\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Batches\Models\Batch;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\Students\Models\Student;

class BulkBatchAssignmentService
{
    /**
     * جلب الطلاب غير المرتبطين بأي شعبة، مع فلترة حسب الشعبة المستهدفة.
     *
     * @param int    $batchId        معرف الشعبة المستهدفة
     * @param string $locationFilter نوع الفلترة الجغرافية: same_location | no_location | all
     * @return array ['batch' => Batch, 'students' => Collection]
     */
    public function getUnassignedStudents(int $batchId, string $locationFilter = 'same_location'): array
    {
        $batch = Batch::with(['academicBranch', 'instituteBranch'])->findOrFail($batchId);

        $query = Student::query()
            ->with(['branch', 'instituteBranch'])
            // فقط الطلاب الذين ليس لديهم أي ارتباط بشعبة
            ->whereDoesntHave('batchStudents')
            // فلترة حسب الفرع الأكاديمي: نفس الفرع أو بدون فرع
            ->where(function ($q) use ($batch) {
                $q->where('branch_id', $batch->academic_branch_id)
                  ->orWhereNull('branch_id');
            });

        // فلترة حسب الموقع الجغرافي
        $this->applyLocationFilter($query, $batch, $locationFilter);

        $students = $query->orderBy('id', 'asc')->get();

        // إضافة حقل assignment_status لكل طالب
        $students->each(function ($student) use ($batch) {
            $student->assignment_status = $this->determineAssignmentStatus($student, $batch);
        });

        return [
            'batch'    => $batch,
            'students' => $students,
        ];
    }

    /**
     * إضافة مجموعة طلاب إلى شعبة معينة.
     *
     * @param int   $batchId    معرف الشعبة
     * @param array $studentIds مصفوفة معرفات الطلاب
     * @return array ملخص العملية
     */
    public function bulkAssign(int $batchId, array $studentIds): array
    {
        $batch = Batch::findOrFail($batchId);

        // التحقق من أن جميع الطلاب ليس لديهم ارتباط بشعبة
        $alreadyAssigned = BatchStudent::whereIn('student_id', $studentIds)->pluck('student_id');

        if ($alreadyAssigned->isNotEmpty()) {
            return [
                'success' => false,
                'message' => 'بعض الطلاب مرتبطون بشعبة بالفعل',
                'already_assigned_ids' => $alreadyAssigned->toArray(),
            ];
        }

        $assignedCount = 0;
        $locationUpdatedCount = 0;

        DB::transaction(function () use ($batch, $studentIds, &$assignedCount, &$locationUpdatedCount) {
            foreach ($studentIds as $studentId) {
                $student = Student::find($studentId);

                if (!$student) {
                    Log::warning("BulkAssign: Student ID {$studentId} not found, skipping.");
                    continue;
                }

                // إنشاء سجل الربط مع الشعبة
                BatchStudent::create([
                    'batch_id'   => $batch->id,
                    'student_id' => $student->id,
                ]);

                $assignedCount++;

                // تحديث الموقع الجغرافي للطلاب بدون موقع
                if (is_null($student->institute_branch_id) && $batch->institute_branch_id) {
                    $student->update([
                        'institute_branch_id' => $batch->institute_branch_id,
                    ]);
                    $locationUpdatedCount++;
                }
            }
        });

        return [
            'success'                => true,
            'total_assigned'         => $assignedCount,
            'location_updated_count' => $locationUpdatedCount,
            'batch_id'               => $batch->id,
            'batch_name'             => $batch->name,
        ];
    }

    /**
     * تطبيق فلترة الموقع الجغرافي على الاستعلام.
     */
    private function applyLocationFilter($query, Batch $batch, string $locationFilter): void
    {
        switch ($locationFilter) {
            case 'same_location':
                // طلاب نفس الموقع الجغرافي للشعبة + طلاب بدون موقع جغرافي
                $query->where(function ($q) use ($batch) {
                    $q->where('institute_branch_id', $batch->institute_branch_id)
                      ->orWhereNull('institute_branch_id');
                });
                break;

            case 'no_location':
                // فقط طلاب بدون موقع جغرافي
                $query->whereNull('institute_branch_id');
                break;

            case 'all':
                // الجميع — لا نضيف أي شرط إضافي
                break;

            default:
                // الافتراضي: نفس الموقع + بدون موقع
                $query->where(function ($q) use ($batch) {
                    $q->where('institute_branch_id', $batch->institute_branch_id)
                      ->orWhereNull('institute_branch_id');
                });
                break;
        }
    }

    /**
     * تحديد حالة التوافق لكل طالب بالنسبة للشعبة المستهدفة.
     */
    private function determineAssignmentStatus(Student $student, Batch $batch): string
    {
        $hasBranch   = !is_null($student->branch_id);
        $hasLocation = !is_null($student->institute_branch_id);

        if (!$hasBranch && !$hasLocation) {
            return 'no_branch_no_location';
        }

        if (!$hasBranch) {
            return 'no_branch';
        }

        if (!$hasLocation) {
            return 'no_location';
        }

        // الطالب لديه فرع وموقع — نتحقق من المطابقة
        return 'matching';
    }
}
