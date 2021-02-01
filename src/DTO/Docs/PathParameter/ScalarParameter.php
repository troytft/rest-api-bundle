<?php

namespace RestApiBundle\DTO\Docs\PathParameter;

use RestApiBundle;

class ScalarParameter implements RestApiBundle\DTO\Docs\PathParameter\PathParameterInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\Docs\Types\ScalarInterface
     */
    private $type;

    public function __construct(string $name, RestApiBundle\DTO\Docs\Types\ScalarInterface $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): RestApiBundle\DTO\Docs\Types\ScalarInterface
    {
        return $this->type;
    }
}
