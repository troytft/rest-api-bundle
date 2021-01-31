<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use function lcfirst;
use function sprintf;
use function strpos;
use function substr;
use cebe\openapi\spec as OpenApi;

class ResponseModelResolver
{
    /**
     * @var array<string, OpenApi\Schema>
     */
    private $classCache = [];

    /**
     * @var RestApiBundle\Services\Docs\Types\TypeHintTypeReader
     */
    private $typeHintReader;

    /**
     * @var RestApiBundle\Services\Docs\Types\DocBlockTypeReader
     */
    private $docBlockReader;

    public function __construct(
        RestApiBundle\Services\Docs\Types\TypeHintTypeReader $typeHintReader,
        RestApiBundle\Services\Docs\Types\DocBlockTypeReader $docBlockReader
    ) {
        $this->typeHintReader = $typeHintReader;
        $this->docBlockReader = $docBlockReader;
    }

    public function resolveByClass(string $class, $nullable = false): OpenApi\Schema
    {
        $cacheKey = sprintf('%s-%s', $class, $nullable);
        if (isset($this->classCache[$cacheKey])) {
            return $this->classCache[$cacheKey];
        }

        if (!RestApiBundle\Helper\ClassInterfaceChecker::isResponseModel($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s is not a response model.', $class));
        }

        $properties = [];

        $reflectedClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $reflectedMethods = $reflectedClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectedMethods as $reflectionMethod) {
            if (strpos($reflectionMethod->getName(), 'get') !== 0) {
                continue;
            }

            $propertyName = lcfirst(substr($reflectionMethod->getName(), 3));
            $propertySchema = $this->convert($this->getReturnType($reflectionMethod));

            $properties[$propertyName] = $propertySchema;
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => false,
        ]);

        $this->classCache[$cacheKey]  = new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'nullable' => $nullable,
            'properties' => $properties,
        ]);

        return $this->classCache[$cacheKey];
    }

    private function getReturnType(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        $result = $this->docBlockReader->getReturnType($reflectionMethod) ?: $this->typeHintReader->getReturnType($reflectionMethod);
        if (!$result) {
            $context = sprintf('%s::%s', $reflectionMethod->class, $reflectionMethod->name);
            throw new RestApiBundle\Exception\Docs\InvalidDefinitionException(new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException(), $context);
        }

        return $result;
    }

    private function convert(RestApiBundle\DTO\Docs\Types\TypeInterface $type): OpenApi\Schema
    {
        if ($type instanceof RestApiBundle\DTO\Docs\Types\ArrayType) {
            $result = $this->convertArrayType($type);
        } elseif ($type instanceof RestApiBundle\DTO\Docs\Types\ScalarInterface) {
            $result = $this->convertScalarType($type);
        } elseif ($type instanceof RestApiBundle\DTO\Docs\Types\ClassType) {
            $result = $this->convertClassType($type);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertScalarType(RestApiBundle\DTO\Docs\Types\ScalarInterface $scalarType): OpenApi\Schema
    {
        switch (true) {
            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\StringType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\IntegerType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\FloatType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\BooleanType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertArrayType(RestApiBundle\DTO\Docs\Types\ArrayType $arrayType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $arrayType->getNullable(),
            'items' => $this->convert($arrayType->getInnerType()),
        ]);
    }

    private function convertClassType(RestApiBundle\DTO\Docs\Types\ClassType $classType): OpenApi\Schema
    {
        switch (true) {
            case RestApiBundle\Helper\ClassInterfaceChecker::isResponseModel($classType->getClass()):
                $result = $this->resolveByClass($classType->getClass(), $classType->getNullable());

                break;

            case RestApiBundle\Helper\ClassInterfaceChecker::isDateTime($classType->getClass()):
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date-time',
                    'nullable' => $classType->getNullable(),
                ]);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported class type %s', $classType->getClass()));
        }

        return $result;
    }
}
