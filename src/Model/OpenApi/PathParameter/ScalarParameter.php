<?php

namespace RestApiBundle\Model\OpenApi\PathParameter;

use RestApiBundle;

class ScalarParameter implements RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\Model\OpenApi\Types\ScalarInterface
     */
    private $type;

    public function __construct(string $name, RestApiBundle\Model\OpenApi\Types\ScalarInterface $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): RestApiBundle\Model\OpenApi\Types\ScalarInterface
    {
        return $this->type;
    }
}
