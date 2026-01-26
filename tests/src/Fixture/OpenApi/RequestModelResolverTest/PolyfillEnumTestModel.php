<?php

namespace Tests\Fixture\OpenApi\RequestModelResolverTest;

use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;
use Tests;

#[Mapper\ExposeAll]
class PolyfillEnumTestModel implements Mapper\ModelInterface
{
    public Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString $stringRequired;

    public ?Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString $stringNullable;

    /**
     * @var \Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString[]
     */
    public array $sringArrayRequired;

    /**
     * @var \Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString[]|null
     */
    public ?array $stringArrayNullable;

    #[Assert\Choice(callback: [Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::class, 'getValues'])]
    public string $choiceCallbackStringRequired;

    #[Assert\Choice(callback: [Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::class, 'getValues'])]
    public ?string $choiceCallbackStringNullable;

    /**
     * @var string[]
     */
    #[Assert\Choice(callback: [Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::class, 'getValues'], multiple: true)]
    public array $choiceCallbackStringArrayRequired;

    /**
     * @var string[]|null
     */
    #[Assert\Choice(callback: [Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::class, 'getValues'], multiple: true)]
    public ?array $choiceCallbackStringArrayNullable;

    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::CREATED,
        Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::PUBLISHED,
    ])]
    public ?string $choiceInlineEnumString;

    /**
     * @var string[]
     */
    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::CREATED,
        Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::PUBLISHED,
    ], multiple: true)]
    public array $choiceInlineStringArrayRequired;

    /**
     * @var string[]|null
     */
    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::CREATED,
        Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::PUBLISHED,
    ], multiple: true)]
    public ?array $choiceInlineStringArrayNullable;
}
