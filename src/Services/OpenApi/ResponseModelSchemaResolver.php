<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use function lcfirst;
use function ltrim;
use function sprintf;
use function strpos;
use function substr;
use cebe\openapi\spec as OpenApi;

class ResponseModelSchemaResolver
{
    /**
     * @var array<string, RestApiBundle\DTO\OpenApi\Types\ObjectType>
     */
    private $objectClassCache = [];

    /**
     * @var RestApiBundle\Services\OpenApi\Schema\TypeHintReader
     */
    private $typeHintReader;

    /**
     * @var RestApiBundle\Services\OpenApi\Schema\DocBlockReader
     */
    private $docBlockReader;

    public function __construct(
        RestApiBundle\Services\OpenApi\Schema\TypeHintReader $typeHintReader,
        RestApiBundle\Services\OpenApi\Schema\DocBlockReader $docBlockReader
    ) {
        $this->typeHintReader = $typeHintReader;
        $this->docBlockReader = $docBlockReader;
    }

    public function resolveByClass(string $class, $isNullable = false): OpenApi\Schema
    {
        return $this->convertObjectType($this->resolveObjectTypeByClass($class, $isNullable));
    }

    private function resolveObjectTypeByClass(string $class, bool $isNullable): RestApiBundle\DTO\OpenApi\Types\ObjectType
    {
        $class = ltrim($class, '\\');

        $cacheKey = sprintf('%s-%s', $class, $isNullable);
        if (isset($this->objectClassCache[$cacheKey])) {
            return $this->objectClassCache[$cacheKey];
        }

        $reflectionClass = RestApiBundle\Services\ReflectionClassStore::get($class);
        if (!$reflectionClass->implementsInterface(RestApiBundle\ResponseModelInterface::class)) {
            throw new \InvalidArgumentException();
        }

        $properties = [];
        $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectionMethods as $reflectionMethod) {
            if (strpos($reflectionMethod->getName(), 'get') !== 0) {
                continue;
            }

            $propertyName = lcfirst(substr($reflectionMethod->getName(), 3));
            $properties[$propertyName] = $this->getByReflectionMethod($reflectionMethod);
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\OpenApi\Types\StringType(false);

        $this->objectClassCache[$cacheKey] = new RestApiBundle\DTO\OpenApi\Types\ObjectType($properties, $isNullable);

        return $this->objectClassCache[$cacheKey];
    }

    private function getByReflectionMethod(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\OpenApi\Types\TypeInterface
    {
        $schema = $this->docBlockReader->getMethodReturnSchema($reflectionMethod) ?: $this->typeHintReader->getMethodReturnSchema($reflectionMethod);

        return $schema;
    }

    private function convertSchemaType(RestApiBundle\DTO\OpenApi\Types\TypeInterface $schemaType): OpenApi\Schema
    {
        if ($schemaType instanceof RestApiBundle\DTO\OpenApi\Types\ObjectType) {
            $result = $this->convertObjectType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Types\ArrayType) {
            $result = $this->convertArrayType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Types\ScalarInterface) {
            $result = $this->convertScalarType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Types\ClassType) {
            $result = $this->convertClassType($schemaType);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertScalarType(RestApiBundle\DTO\OpenApi\Types\ScalarInterface $scalarType): OpenApi\Schema
    {
        if ($scalarType instanceof RestApiBundle\DTO\OpenApi\Types\StringType) {
            $result = $this->convertStringType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Types\IntegerType) {
            $result = $this->convertIntegerType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Types\FloatType) {
            $result = $this->convertFloatType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Types\BooleanType) {
            $result = $this->convertBooleanType($scalarType);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertObjectType(RestApiBundle\DTO\OpenApi\Types\ObjectType $objectType): OpenApi\Schema
    {
        $properties = [];

        foreach ($objectType->getProperties() as $key => $propertyType) {
            $properties[$key] = $this->convertSchemaType($propertyType);
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'nullable' => $objectType->getNullable(),
            'properties' => $properties,
        ]);
    }

    private function convertArrayType(RestApiBundle\DTO\OpenApi\Types\ArrayType $arrayType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $arrayType->getNullable(),
            'items' => $this->convertSchemaType($arrayType->getInnerType()),
        ]);
    }

    private function convertStringType(RestApiBundle\DTO\OpenApi\Types\StringType $stringType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => $stringType->getNullable(),
        ]);
    }

    private function convertIntegerType(RestApiBundle\DTO\OpenApi\Types\IntegerType $integerType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::INTEGER,
            'nullable' => $integerType->getNullable(),
        ]);
    }

    private function convertFloatType(RestApiBundle\DTO\OpenApi\Types\FloatType $floatType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::NUMBER,
            'format' => 'double',
            'nullable' => $floatType->getNullable(),
        ]);
    }

    private function convertBooleanType(RestApiBundle\DTO\OpenApi\Types\BooleanType $booleanType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::BOOLEAN,
            'nullable' => $booleanType->getNullable(),
        ]);
    }

    private function convertClassType(RestApiBundle\DTO\OpenApi\Types\ClassType $classType): OpenApi\Schema
    {
        switch (true) {
            case RestApiBundle\Helper\ClassHelper::isResponseModel($classType->getClass()):
                $result = $this->resolveByClass($classType->getClass(), $classType->getNullable());

                break;

            case RestApiBundle\Helper\ClassHelper::isDateTime($classType->getClass()):
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
