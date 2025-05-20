<?php
declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper;

use RestApiBundle;

class MappingException extends \RuntimeException implements RestApiBundle\Exception\ExceptionInterface
{
    /**
     * @param array<string, string[]> $properties
     */
    public function __construct(private array $properties)
    {
        parent::__construct();
    }

    /**
     * @return array<string, string[]>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
