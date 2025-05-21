<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\OpenApi\ResponseModel;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class UnknownTypeException extends \InvalidArgumentException
{
    public function __construct(private PropertyInfo\Type $type)
    {
        parent::__construct(sprintf('Unknown type "%s"', RestApiBundle\Helper\PropertyInfoHelper::format($this->type)));
    }
}
