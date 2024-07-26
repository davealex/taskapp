<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum TaskPriorityEnum: string
{
    use Values;

    case High = 'high';

    case Medium = 'medium';

    case Low = 'low';
}
