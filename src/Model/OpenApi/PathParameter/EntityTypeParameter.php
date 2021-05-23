<?php

namespace RestApiBundle\Model\OpenApi\PathParameter;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

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

    public function __construct(string $name, PropertyInfo\Type $class, string $fieldName)
    {
        $this->name = $name;
        $this->classType = $class;
        $this->fieldName = $fieldName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassType(): PropertyInfo\Type
    {
        return $this->classType;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
