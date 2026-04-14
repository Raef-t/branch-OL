<?php

namespace Modules\Exams\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Exams\Filters\ExamFilter;
use Modules\ExamTypes\Models\ExamType;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Carbon\Carbon;

use App\Models\Concerns\RestrictDeletion;

class Exam extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'الامتحان';

    protected array $deletionRestrictedRelations = [
        'results' => 'نتائج الطلاب',
    ];

    protected $table = 'exams';

    protected $fillable = [
        'batch_subject_id',
        'name',
        'exam_date',
        'exam_time',
        'exam_end_time',
        'total_marks',
        'passing_marks',
        'status',
        'exam_type_id',
        'remarks',
    ];

    protected $casts = [
        'exam_date'     => 'date',
        'exam_time'     => 'string',
        'exam_end_time' => 'string',
        'total_marks'   => 'integer',
        'passing_marks' => 'integer',
        'status'        => 'string',
        'exam_type_id'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function canBeModified(): bool
    {
        $examDate = $this->exam_date->copy()->startOfDay();
        $today = now()->startOfDay();

        if ($examDate->lessThan($today)) {
            return false;
        }
       
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        return true;
    }


    /* =======================
     | Scopes
     ======================= */

    public function scopeByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('exam_date', $date);
    }

    public function scopeFilter(Builder $query, ExamFilter $filter): Builder
    {
        return $query
            ->when($filter->branchId, function ($q, $branchId) {
                $q->whereHas('batchSubject.batch', function ($q) use ($branchId) {
                    $q->where('institute_branch_id', $branchId);
                });
            })
            ->when($filter->batchId, function ($q, $batchId) {
                $q->whereHas('batchSubject', function ($q) use ($batchId) {
                    $q->where('batch_id', $batchId);
                });
            })
            ->when($filter->gender, function ($q, $gender) {
                $q->whereHas('batchSubject.batch', function ($q) use ($gender) {
                    $q->forGender($gender);
                });
            });
    }

    public function scopeFilterByVerification(
        Builder $query,
        \Modules\Exams\Filters\BatchAttendanceVerificationFilter $filter
    ): Builder {
        return $query
            ->when($filter->instituteBranchId, function ($q, $branchId) {
                $q->whereHas(
                    'batchSubject.batch',
                    fn($qb) =>
                    $qb->where('institute_branch_id', $branchId)
                );
            })
            ->when($filter->batchId, function ($q, $batchId) {
                $q->whereHas(
                    'batchSubject',
                    fn($qb) =>
                    $qb->where('batch_id', $batchId)
                );
            })
            ->when($filter->subjectId, function ($q, $subjectId) {
                $q->whereHas(
                    'batchSubject.subject',
                    fn($qb) =>
                    $qb->where('id', $subjectId)
                );
            });
    }

    public function batchSubject()
    {
        return $this->belongsTo(
            \Modules\BatchSubjects\Models\BatchSubject::class,
            'batch_subject_id'
        );
    }

    public function examType()
    {
        return $this->belongsTo(
            ExamType::class,
            'exam_type_id',
            'id'
        );
    }

    public function results()
    {
        return $this->hasMany(\Modules\ExamResults\Models\ExamResult::class, 'exam_id');
    }
}
