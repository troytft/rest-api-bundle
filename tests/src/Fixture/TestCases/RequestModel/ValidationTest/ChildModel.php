<?php

namespace Tests\Fixture\TestCases\RequestModel\ValidationTest;

use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class ChildModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    #[Assert\Expression(expression: 'false')]
    public ?string $field;
}
