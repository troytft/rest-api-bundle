<?php
declare(strict_types=1);

namespace Tests\Fixture\Mapper\ValidationTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestClearMissingModel implements Mapper\ModelInterface
{
    public string $field = 'default value';
}
