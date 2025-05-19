<?php

namespace Tests\Fixture\OpenApi\RequestModelResolverTest;

use Tests;
use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class PhpEnumTestModel implements Mapper\ModelInterface
{
    public Tests\Fixture\TestApp\Enum\PhpStringEnum $stringRequired;

    public ?Tests\Fixture\TestApp\Enum\PolyfillStringEnum $stringNullable;

    /**
     * @var Tests\Fixture\TestApp\Enum\PhpStringEnum[]
     */
    public array $arrayRequired;

    /**
     * @var Tests\Fixture\TestApp\Enum\PhpStringEnum[]|null
     */
    public ?array $arrayNullable;

    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\PhpStringEnum::CREATED,
        Tests\Fixture\TestApp\Enum\PhpStringEnum::PUBLISHED,
    ])]
    public string $choiceInlineStringRequired;

    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\PhpStringEnum::CREATED,
        Tests\Fixture\TestApp\Enum\PhpStringEnum::PUBLISHED,
    ])]
    public ?string $choiceInlineStringNullable;

    /**
     * @var string[]
     */
    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\PhpStringEnum::CREATED,
        Tests\Fixture\TestApp\Enum\PhpStringEnum::PUBLISHED,
    ], multiple: true)]
    public array $choiceInlineStringArrayRequired;

    /**
     * @var string[]|null
     */
    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\PhpStringEnum::CREATED,
        Tests\Fixture\TestApp\Enum\PhpStringEnum::PUBLISHED,
    ], multiple: true)]
    public ?array $choiceInlineStringArrayNullable;
}
