<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use function get_class;
use function lcfirst;
use function ltrim;
use function sprintf;
use function strpos;
use function substr;
use cebe\openapi\spec as OpenApi;
use function var_dump;

class ResponseModelSchemaResolver
{
    /**
     * @var array<string, RestApiBundle\DTO\OpenApi\Schema\ObjectType>
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

    public function resolveByClass(string $class): OpenApi\Schema
    {
        return $this->convertObjectType($this->resolveObjectTypeByClass($class, false));
    }

    private function resolveObjectTypeByClass(string $class, bool $nullable): RestApiBundle\DTO\OpenApi\Schema\ObjectType
    {
        $class = ltrim($class, '\\');

        $cacheKey = sprintf('%s-%s', $class, $nullable);
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

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\OpenApi\Schema\StringType(false);

        $this->objectClassCache[$cacheKey] = new RestApiBundle\DTO\OpenApi\Schema\ObjectType($properties, $nullable);

        return $this->objectClassCache[$cacheKey];
    }

    private function getByReflectionMethod(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface
    {
        $schema = $this->docBlockReader->getMethodReturnSchema($reflectionMethod) ?: $this->typeHintReader->getMethodReturnSchema($reflectionMethod);

        return $schema;
    }

    private function convertSchemaType(RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface $schemaType): OpenApi\Schema
    {
        if ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\ObjectType) {
            $result = $this->convertObjectType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\ArrayType) {
            $result = $this->convertArrayType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\ScalarInterface) {
            $result = $this->convertScalarType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\ClassType) {
            $result = $this->convertClassType($schemaType);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertScalarType(RestApiBundle\DTO\OpenApi\Schema\ScalarInterface $scalarType): OpenApi\Schema
    {
        if ($scalarType instanceof RestApiBundle\DTO\OpenApi\Schema\StringType) {
            $result = $this->convertStringType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Schema\IntegerType) {
            $result = $this->convertIntegerType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Schema\FloatType) {
            $result = $this->convertFloatType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Schema\BooleanType) {
            $result = $this->convertBooleanType($scalarType);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertObjectType(RestApiBundle\DTO\OpenApi\Schema\ObjectType $objectType): OpenApi\Schema
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

    private function convertArrayType(RestApiBundle\DTO\OpenApi\Schema\ArrayType $arrayType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $arrayType->getNullable(),
            'items' => $this->convertSchemaType($arrayType->getInnerType()),
        ]);
    }

    private function convertStringType(RestApiBundle\DTO\OpenApi\Schema\StringType $stringType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => $stringType->getNullable(),
        ]);
    }

    private function convertIntegerType(RestApiBundle\DTO\OpenApi\Schema\IntegerType $integerType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::INTEGER,
            'nullable' => $integerType->getNullable(),
        ]);
    }

    private function convertFloatType(RestApiBundle\DTO\OpenApi\Schema\FloatType $floatType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::NUMBER,
            'format' => 'double',
            'nullable' => $floatType->getNullable(),
        ]);
    }

    private function convertBooleanType(RestApiBundle\DTO\OpenApi\Schema\BooleanType $booleanType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::BOOLEAN,
            'nullable' => $booleanType->getNullable(),
        ]);
    }

    private function convertClassType(RestApiBundle\DTO\OpenApi\Schema\ClassType $classType): OpenApi\Schema
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
