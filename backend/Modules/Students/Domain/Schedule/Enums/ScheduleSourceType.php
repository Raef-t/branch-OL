<?php

declare(strict_types=1);

namespace Modules\Students\Domain\Schedule\Enums;

enum ScheduleSourceType: string
{
    case STUDENT = 'student';
    case BATCH = 'batch';
    case LOCATION = 'location';
}
