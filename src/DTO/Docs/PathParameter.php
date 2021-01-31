<?php

namespace RestApiBundle\DTO\Docs;

use RestApiBundle;

class PathParameter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\Docs\Types\TypeInterface
     */
    private $schema;

    public function __construct(string $name, RestApiBundle\DTO\Docs\Types\TypeInterface $schema)
    {
        $this->name = $name;
        $this->schema = $schema;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getSchema(): Types\TypeInterface
    {
        return $this->schema;
    }

    public function setSchema(Types\TypeInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }
}
