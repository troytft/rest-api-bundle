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
     * @var RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
     */
    private $schema;

    public function __construct(string $name, RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface $schema)
    {
        $this->name = $name;
        $this->schema = $schema;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSchema(): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        return $this->schema;
    }
}
