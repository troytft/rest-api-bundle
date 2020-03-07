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

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getSchema(): Schema\SchemaTypeInterface
    {
        return $this->schema;
    }

    public function setSchema(Schema\SchemaTypeInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }
}
