<?php

namespace Tests\Fixture\OpenApi\RequestModelResolverTest;

use Tests;
use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class TestEnumSchemaModel implements Mapper\ModelInterface
{
    public ?Tests\Fixture\TestApp\Enum\BookStatus $transformerBasedSingleItem;

    /**
     * @var Tests\Fixture\TestApp\Enum\BookStatus[]|null
     */
    public ?array $transformerBasedMultipleItem;

    #[Assert\Choice(callback: 'Tests\Fixture\TestApp\Enum\BookStatus::getValues')]
    public ?string $validatorBasedSingleItem;

    /**
     * @var string[]|null
     */
    #[Assert\Choice(callback: 'Tests\Fixture\TestApp\Enum\BookStatus::getValues', multiple: true)]
    public ?array $validatorBasedMultipleItem;
}
