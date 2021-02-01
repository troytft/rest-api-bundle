<?php

namespace RestApiBundle\DTO\Docs\PathParameter;

use RestApiBundle;

class EntityTypeParameter implements RestApiBundle\DTO\Docs\PathParameter\PathParameterInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\Docs\Types\ClassType
     */
    private $classType;

    /**
     * @var string
     */
    private $fieldName;

    public function __construct(string $name, RestApiBundle\DTO\Docs\Types\ClassType $class, string $fieldName)
    {
        $this->name = $name;
        $this->classType = $class;
        $this->fieldName = $fieldName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassType(): RestApiBundle\DTO\Docs\Types\ClassType
    {
        return $this->classType;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
