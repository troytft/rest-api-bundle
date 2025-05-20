<?php

declare(strict_types=1);

namespace Tests\Fixture\Mapper\ValidationTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestNestedValidationModel implements Mapper\ModelInterface
{
    public ?ChildModel $childModel;

    /**
     * @var ChildModel[]|null
     */
    public ?array $childModels;
}
