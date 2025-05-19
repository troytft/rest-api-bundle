<?php

namespace Tests\Fixture\TestApp\Enum;

enum PhpIntegerEnum: int
{
    case CREATED = 0;
    case PUBLISHED = 1;
    case ARCHIVED = 2;
}
