<?php

namespace Tests\Fixture\ClassImportTest\RequestModel;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class RelativeImport implements Mapper\ModelInterface
{
    public ?Inner $innerModelField;

    /**
     * @var Inner[]|null
     */
    public ?array $innerModelArrayField;
}
