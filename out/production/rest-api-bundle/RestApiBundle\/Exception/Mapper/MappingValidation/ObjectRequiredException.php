<?php

namespace RestApiBundle\Exception\Mapper\MappingValidation;

use RestApiBundle\Exception\Mapper\PathTrait;

class ObjectRequiredException extends \Exception implements MappingValidationExceptionInterface
{
    use PathTrait;

    /**
     * @param array $path
     */
    public function __construct(array $path)
    {
        $this->path = $path;

        parent::__construct();
    }
}
