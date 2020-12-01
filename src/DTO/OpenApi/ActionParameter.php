<?php

namespace RestApiBundle\DTO\OpenApi;

use RestApiBundle;

class ActionParameter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\OpenApi\Schema\TypeInterface|null
     */
    private $schema;

    public function __construct(string $name, ?RestApiBundle\DTO\OpenApi\Schema\TypeInterface $schema)
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

    public function getSchema(): ?Schema\TypeInterface
    {
        return $this->schema;
    }

    public function setSchema(?Schema\TypeInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }
}
