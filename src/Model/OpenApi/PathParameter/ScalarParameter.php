<?php

namespace RestApiBundle\Model\OpenApi\PathParameter;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class ScalarParameter implements RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var PropertyInfo\Type
     */
    private $type;

    public function __construct(string $name, PropertyInfo\Type $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): PropertyInfo\Type
    {
        return $this->type;
    }
}
