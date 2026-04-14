<?php

declare(strict_types=1);

namespace Modules\Students\Filters;

use Modules\Students\Application\Schedule\Data\GetScheduleData;
use Modules\Students\Domain\Schedule\Enums\ScheduleSourceType;
use Illuminate\Http\Request;
use Modules\Shared\Filters\BaseFilter;

final class ScheduleFilter extends BaseFilter
{
    public ScheduleSourceType $type;
    public ?int $id;
    public string $day;
    public ?bool $isDefault;
    public ?int $instituteBranchId;

    protected static function rules(): array
    {
        return [
            'type' => ['required', 'in:student,batch,location'],
            'id' => ['nullable', 'integer'],
            'day' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'institute_branch_id' => ['nullable', 'integer'],
        ];
    }

    protected static function make(Request $request): static
    {
        $filter = new static();

        $filter->type = ScheduleSourceType::from($request->query('type'));
        $filter->id = $request->filled('id') ? (int) $request->query('id') : null;
        $filter->day = $request->query('day', 'today');
        $filter->isDefault = $request->has('is_default')
            ? filter_var($request->query('is_default'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : null;
        $filter->instituteBranchId = $request->filled('institute_branch_id')
            ? (int) $request->query('institute_branch_id')
            : null;

        return $filter;
    }

    public function toDto(): GetScheduleData
    {
        return new GetScheduleData(
            type: $this->type,
            id: $this->id,
            day: $this->day,
            isDefault: $this->isDefault,
            instituteBranchId: $this->instituteBranchId
        );
    }
}
