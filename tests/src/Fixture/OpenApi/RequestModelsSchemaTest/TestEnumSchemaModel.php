<?php

namespace Tests\Fixture\OpenApi\RequestModelsSchemaTest;

use Tests;
use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class TestEnumSchemaModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?Tests\Fixture\Common\Enum\BookStatus $transformerBasedSingleItem;

    /**
     * @var Tests\Fixture\Common\Enum\BookStatus[]|null
     */
    public ?array $transformerBasedMultipleItem;

    #[Assert\Choice(callback: 'Tests\Fixture\Common\Enum\BookStatus::getValues')]
    public ?string $validatorBasedSingleItem;

    /**
     * @var string[]|null
     */
    #[Assert\Choice(callback: 'Tests\Fixture\Common\Enum\BookStatus::getValues', multiple: true)]
    public ?array $validatorBasedMultipleItem;
}
