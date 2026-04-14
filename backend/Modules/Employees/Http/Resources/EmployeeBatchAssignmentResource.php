<?php
namespace Modules\Employees\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeBatchAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // قد يكون resource عبارة عن Employee مباشرة أو مصفوفة
        $employee = $this->resource['employee'] ?? $this->resource;

        // جلب الشعب التي يشرف عليها (تعيينات نشطة فقط)
        $activeAssignments = $employee->batchAssignments()
            ->where('is_active', true)
            ->with('batch:id,name')
            ->get();

        return [
            'employee' => new EmployeeResource($employee),

            // 🔹 الشعب التي يشرف عليها
            'supervised_batches' => $activeAssignments->map(function ($assignment) {
                return [
                    'batch_id'   => $assignment->batch->id,
                    'batch_name' => $assignment->batch->name,
                ];
            }),

            // 🔹 عدد الشعب
            'supervised_batches_count' => $activeAssignments->count(),

            // 🔹 الدفعة التي تم تعيينه عليها في هذه العملية
            'assigned_batch_id' => $this->resource['assigned_batch_id'] ?? null,
        ];
    }
}
