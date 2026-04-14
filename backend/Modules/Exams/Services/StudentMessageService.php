<?php

namespace Modules\Exams\Services;

use Modules\Exams\Models\StudentMessage;
use Illuminate\Support\Carbon;

class StudentMessageService
{
    public function storeMessages(array $studentIds, ?int $templateId = null)

    {
        $now = Carbon::now();

        $data = collect($studentIds)->map(fn($id) => [
            'student_id' => $id,
            'template_id' => $templateId,
            'status' => 'sent',
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        StudentMessage::insert($data);

        return count($studentIds);
    }
}
