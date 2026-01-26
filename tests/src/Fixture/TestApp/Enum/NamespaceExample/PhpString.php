<?php

namespace Tests\Fixture\TestApp\Enum\NamespaceExample;

enum PhpString: string
{
    case CREATED = 'created';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
