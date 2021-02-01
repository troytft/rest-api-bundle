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
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $fieldName;

    public function __construct(string $name, string $class, string $fieldName)
    {
        $this->name = $name;
        $this->class = $class;
        $this->fieldName = $fieldName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
