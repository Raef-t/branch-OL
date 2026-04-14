<?php

declare(strict_types=1);

namespace Modules\Students\Application\Schedule\Data;

use Modules\Students\Domain\Schedule\Enums\ScheduleSourceType;

readonly class GetScheduleData
{
    public function __construct(
        public ScheduleSourceType $type,
        public ?int $id, // nullable الآن
        public string $day,
        public ?bool $isDefault,
        public ?int $instituteBranchId,
    ) {}
}
