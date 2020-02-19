<?php

namespace RestApiBundle\Services\Docs\Type;

use RestApiBundle;

class TypeReader
{
    /**
     * @var RestApiBundle\Services\Docs\Type\Adapter\DocBlockReader
     */
    private $docBlockReader;

    /**
     * @var RestApiBundle\Services\Docs\Type\Adapter\TypeHintReader
     */
    private $typeHintReader;

    /**
     * @var RestApiBundle\Services\Docs\Type\Adapter\ResponseModelReader
     */
    private $responseModelReader;

    public function __construct(
        RestApiBundle\Services\Docs\Type\Adapter\DocBlockReader $docBlockReader,
        RestApiBundle\Services\Docs\Type\Adapter\TypeHintReader $typeHintReader,
        RestApiBundle\Services\Docs\Type\Adapter\ResponseModelReader $responseModelReader
    ) {
        $this->docBlockReader = $docBlockReader;
        $this->typeHintReader = $typeHintReader;
        $this->responseModelReader = $responseModelReader;
    }

    public function getReturnTypeByReflectionMethod(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        $type = $this->docBlockReader->getReturnType($reflectionMethod) ?: $this->typeHintReader->getReturnType($reflectionMethod);

        if ($type instanceof RestApiBundle\DTO\Docs\Schema\ClassType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($type->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $type = $this->responseModelReader->getTypeByClass($type->getClass(), $type->getNullable());
        } elseif ($type instanceof RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($type->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $objectType = $this->responseModelReader->getTypeByClass($type->getClass(), $type->getNullable());
            $type = new RestApiBundle\DTO\Docs\Schema\ArrayType($objectType, $objectType->getNullable());
        }

        return $type;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return RestApiBundle\DTO\Docs\NamedTyped[]
     */
    public function getActionParametersByReflectionMethod(\ReflectionMethod $reflectionMethod): array
    {
        $result = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $type = $this->typeHintReader->getParameterTypeByReflectionParameter($reflectionParameter);
            $result[] = new RestApiBundle\DTO\Docs\NamedTyped($reflectionParameter->getName(), $type);
        }

        return $result;
    }
}
