<?php

namespace RestApiBundle\DTO\Docs;

use RestApiBundle;

class SchemaWithName
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\Docs\Schema\TypeInterface
     */
    private $schema;

    public function __construct(string $name, RestApiBundle\DTO\Docs\Schema\TypeInterface $schema)
    {
        $this->name = $name;
        $this->schema = $schema;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSchema(): RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        return $this->schema;
    }
}
