<?php

namespace RestApiBundle\Exception\Mapper\MappingValidation;

use RestApiBundle\Exception\Mapper\PathTrait;

class CanNotBeNullException extends \Exception implements MappingValidationExceptionInterface
{
    use PathTrait;
    
    public function __construct(array $path)
    {
        $this->path = $path;

        parent::__construct();
    }
}
