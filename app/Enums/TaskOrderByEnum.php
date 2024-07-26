<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum TaskOrderByEnum: string
{
    use Values;

    case Latest = 'latest';

    case Oldest = 'oldest';
}
