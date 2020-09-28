<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use function lcfirst;
use function ltrim;
use function sprintf;
use function strpos;
use function substr;

class ResponseCollector
{
    /**
     * @var array<string, RestApiBundle\DTO\Docs\Schema\ObjectType>
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

    public function getByReflectionMethod(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        try {
            $schema = $this->docBlockReader->getMethodReturnSchema($reflectionMethod) ?: $this->typeHintReader->getMethodReturnSchema($reflectionMethod);

            if (!$schema) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException();
            }

            if ($schema instanceof RestApiBundle\DTO\Docs\Schema\ClassType) {
                $schema = $this->resolveClassType($schema);
            } elseif ($schema instanceof RestApiBundle\DTO\Docs\Schema\ArrayType && $schema->getInnerType() instanceof RestApiBundle\DTO\Docs\Schema\ClassType) {
                /** @var RestApiBundle\DTO\Docs\Schema\ClassType $innerType */
                $innerType = $schema->getInnerType();
                $schema = new RestApiBundle\DTO\Docs\Schema\ArrayType($this->resolveClassType($innerType), $schema->getNullable());
            }
        } catch (RestApiBundle\Exception\Docs\InvalidDefinition\BaseInvalidDefinitionException $exception) {
            $context = sprintf('%s::%s', $reflectionMethod->class, $reflectionMethod->name);
            throw new RestApiBundle\Exception\Docs\InvalidDefinitionException($exception, $context);
        }

        return $schema;
    }

    private function resolveClassType(RestApiBundle\DTO\Docs\Schema\ClassType $classType): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        if ($classType->getClass() === \DateTime::class) {
            $result = new RestApiBundle\DTO\Docs\Schema\DateTimeType($classType->getNullable());
        } elseif (RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($classType->getClass())) {
            $result = $this->getResponseModelSchemaByClass($classType->getClass(), $classType->getNullable());
        } else {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return $result;
    }

    private function getResponseModelSchemaByClass(string $class, bool $isNullable): RestApiBundle\DTO\Docs\Schema\ObjectType
    {
        $class = ltrim($class, '\\');
        $cacheKey = sprintf('%s-%d', $class, $isNullable);

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

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\Docs\Schema\StringType(false);

        $this->objectClassCache[$cacheKey] = new RestApiBundle\DTO\Docs\Schema\ObjectType($properties, $isNullable);

        return $this->objectClassCache[$cacheKey];
    }
}
