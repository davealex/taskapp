<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum TaskStatusEnum: string
{
    use Values;

    case Pending = 'pending';

    case InProgress = 'in progress';

    case Completed = 'completed';
}
