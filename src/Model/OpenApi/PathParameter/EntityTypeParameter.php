<?php

namespace RestApiBundle\Model\OpenApi\PathParameter;

use RestApiBundle;

class EntityTypeParameter implements RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\Model\OpenApi\Types\ClassType
     */
    private $classType;

    /**
     * @var string
     */
    private $fieldName;

    public function __construct(string $name, RestApiBundle\Model\OpenApi\Types\ClassType $class, string $fieldName)
    {
        $this->name = $name;
        $this->classType = $class;
        $this->fieldName = $fieldName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassType(): RestApiBundle\Model\OpenApi\Types\ClassType
    {
        return $this->classType;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
