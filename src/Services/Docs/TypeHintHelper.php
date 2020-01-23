<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;

class TypeHintHelper
{
    /**
     * @var RestApiBundle\Services\Docs\ResponseModelHelper
     */
    private $responseModelHelper;

    public function __construct(RestApiBundle\Services\Docs\ResponseModelHelper $responseModelHelper)
    {
        $this->responseModelHelper = $responseModelHelper;
    }

    public function getReturnTypeByReflectionMethod(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        if (!$reflectionMethod->getReturnType()) {
            return null;
        }

        $class = (string) $reflectionMethod->getReturnType();

        if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($class)) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        $objectType = $this->responseModelHelper->getObjectTypeByClass($class);

        if ($reflectionMethod->getReturnType()->allowsNull()) {
            $objectType->setIsNullable(true);
        }

        return $objectType;
    }
}
