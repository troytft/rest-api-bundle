<?php

namespace Tests\Fixture\ClassImportTest\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class FullyQualifiedNameImport implements Mapper\ModelInterface
{
    public ?Tests\Fixture\ClassImportTest\RequestModel\Inner $innerModelField;

    /**
     * @var Tests\Fixture\ClassImportTest\RequestModel\Inner[]|null
     */
    public ?array $innerModelArrayField;
}
