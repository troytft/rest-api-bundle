<?php

namespace Tests\Fixture\Mapper\ValidationTest;

use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class ChildModel implements Mapper\ModelInterface
{
    #[Assert\Expression(expression: 'false')]
    public ?string $field;
}
