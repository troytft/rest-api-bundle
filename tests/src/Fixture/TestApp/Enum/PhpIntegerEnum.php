<?php

declare(strict_types=1);

namespace Tests\Fixture\TestApp\Enum;

enum PhpIntegerEnum: int
{
    case CREATED = 0;
    case PUBLISHED = 1;
    case ARCHIVED = 2;
}
