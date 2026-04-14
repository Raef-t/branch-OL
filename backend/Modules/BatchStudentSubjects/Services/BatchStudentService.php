<?php
namespace Modules\BatchStudents\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\BatchSubjects\Models\BatchSubject;

class BatchStudentService
{
    public function enroll(array $data): BatchStudent
    {
        return DB::transaction(function () use ($data) {

            $batchStudent = BatchStudent::create([
                'student_id' => $data['student_id'],
                'batch_id'   => $data['batch_id'],
                'is_partial' => $data['is_partial'] ?? false,
            ]);

            if (!empty($data['is_partial'])) {
                $this->handlePartialEnrollment(
                    $batchStudent,
                    $data['batch_subject_ids'] ?? []
                );
            }

            return $batchStudent->load([
                'student',
                'batch',
                'batchSubjects'
            ]);
        });
    }

    public function updatePartialSubjects(int $id, array $data): BatchStudent
    {
        $batchStudent = BatchStudent::findOrFail($id);

        if (!$batchStudent->is_partial) {
            throw ValidationException::withMessages([
                'is_partial' => ['لا يمكن تعديل مواد طالب مسجل بكامل الدفعة']
            ]);
        }

        $this->handlePartialEnrollment(
            $batchStudent,
            $data['batch_subject_ids']
        );

        return $batchStudent->load('batchSubjects');
    }

    protected function handlePartialEnrollment(
        BatchStudent $batchStudent,
        array $subjectIds
    ): void {
        if (empty($subjectIds)) {
            throw ValidationException::withMessages([
                'batch_subject_ids' => ['يجب تحديد مادة واحدة على الأقل']
            ]);
        }

        $validIds = BatchSubject::where('batch_id', $batchStudent->batch_id)
            ->whereIn('id', $subjectIds)
            ->pluck('id')
            ->toArray();

        if (count($validIds) !== count($subjectIds)) {
            throw ValidationException::withMessages([
                'batch_subject_ids' => ['بعض المواد لا تنتمي إلى هذه الدفعة']
            ]);
        }

        $batchStudent->batchSubjects()->sync($validIds);
    }
}
