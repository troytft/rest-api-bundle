<?php

namespace Tests\Fixture\OpenApi\RequestModelResolverTest;

use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;
use Tests;

#[Mapper\ExposeAll]
class PhpEnumTestModel implements Mapper\ModelInterface
{
    public Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString $stringRequired;

    public ?Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString $stringNullable;

    /**
     * @var \Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString[]
     */
    public array $arrayRequired;

    /**
     * @var \Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString[]|null
     */
    public ?array $arrayNullable;

    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::CREATED,
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::PUBLISHED,
    ])]
    public string $choiceInlineStringRequired;

    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::CREATED,
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::PUBLISHED,
    ])]
    public ?string $choiceInlineStringNullable;

    /**
     * @var string[]
     */
    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::CREATED,
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::PUBLISHED,
    ], multiple: true)]
    public array $choiceInlineStringArrayRequired;

    /**
     * @var string[]|null
     */
    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::CREATED,
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::PUBLISHED,
    ], multiple: true)]
    public ?array $choiceInlineStringArrayNullable;
}
