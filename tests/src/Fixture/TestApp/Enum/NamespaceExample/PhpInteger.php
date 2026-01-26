<?php

namespace Tests\Fixture\TestApp\Enum\NamespaceExample;

enum PhpInteger: int
{
    case CREATED = 0;
    case PUBLISHED = 1;
    case ARCHIVED = 2;
}
