<?php
declare(strict_types=1);

namespace Tests\Fixture\TestApp\Enum;

enum PhpStringEnum: string
{
    case CREATED = 'created';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
