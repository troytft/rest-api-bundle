<?php

namespace Tests\Fixture\ClassImportTest\RequestModel;

use RestApiBundle\Mapping\Mapper;
use Tests\Fixture\ClassImportTest\RequestModel\Inner AS InnerModel;

#[Mapper\ExposeAll]
class UseAlias implements Mapper\ModelInterface
{
    public ?InnerModel $innerModelField;

    /**
     * @var InnerModel[]|null
     */
    public ?array $innerModelArrayField;
}
