<?php

namespace Modules\Attendances\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceStatisticsService
{
    public function calculate(array $filters = []): array
    {
        $query = DB::table('attendances');


        /* =====================
         | فلترة حسب الفرع
         ===================== */
        if (!empty($filters['institute_branch_id'])) {
            $query->where('institute_branch_id', $filters['institute_branch_id']);
        }

        /* =====================
         | فلترة حسب الشعب
         ===================== */
        if (!empty($filters['batch_ids'])) {
            $query->whereIn('batch_id', (array) $filters['batch_ids']);
        }

        /* =====================
         | فلترة حسب المدة الزمنية
         ===================== */
        if (!empty($filters['period'])) {
            match ($filters['period']) {
                'last_week' => $query->whereDate(
                    'attendance_date',
                    '>=',
                    Carbon::now()->subWeek()
                ),

                'last_month' => $query->whereDate(
                    'attendance_date',
                    '>=',
                    Carbon::now()->subMonth()
                ),

                default => null,
            };
        }

        /* =====================
         | الحساب
         ===================== */
        $total = (clone $query)->count();

        if ($total === 0) {
            return [
                'attendance_percentage' => 0,
                'absence_percentage'    => 0,
                'present_count'         => 0,
                'absent_count'          => 0,
                'total_records'         => 0,
            ];
        }

        $present = (clone $query)
            ->where('status', 'present')
            ->count();

        $absent = (clone $query)
            ->where('status', 'absent')
            ->count();

        return [
            'attendance_percentage' => round(($present / $total) * 100, 2),
            'absence_percentage'    => round(($absent / $total) * 100, 2),
            'present_count'         => $present,
            'absent_count'          => $absent,
            'total_records'         => $total,
        ];
    }
}
