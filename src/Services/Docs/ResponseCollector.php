<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use function lcfirst;
use function ltrim;
use function sprintf;
use function strpos;
use function substr;

class ResponseCollector
{
    /**
     * @var array<string, RestApiBundle\DTO\Docs\Types\ObjectType>
     */
    private $objectClassCache = [];

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

    public function getByReflectionMethod(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        try {
            $schema = $this->docBlockReader->getReturnType($reflectionMethod) ?: $this->typeHintReader->getReturnType($reflectionMethod);

            if (!$schema) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException();
            }

            if ($schema instanceof RestApiBundle\DTO\Docs\Types\ClassType) {
                $schema = $this->resolveClassType($schema);
            } elseif ($schema instanceof RestApiBundle\DTO\Docs\Types\ArrayType && $schema->getInnerType() instanceof RestApiBundle\DTO\Docs\Types\ClassType) {
                /** @var RestApiBundle\DTO\Docs\Types\ClassType $innerType */
                $innerType = $schema->getInnerType();
                $schema = new RestApiBundle\DTO\Docs\Types\ArrayType($this->resolveClassType($innerType), $schema->getNullable());
            }
        } catch (RestApiBundle\Exception\Docs\InvalidDefinition\BaseInvalidDefinitionException $exception) {
            $context = sprintf('%s::%s', $reflectionMethod->class, $reflectionMethod->name);
            throw new RestApiBundle\Exception\Docs\InvalidDefinitionException($exception, $context);
        }

        return $schema;
    }

    private function resolveClassType(RestApiBundle\DTO\Docs\Types\ClassType $classType): RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        if ($classType->getClass() === \DateTime::class) {
            $result = new RestApiBundle\DTO\Docs\Types\DateTimeType($classType->getNullable());
        } elseif (RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($classType->getClass())) {
            $result = $this->getResponseModelSchemaByClass($classType->getClass(), $classType->getNullable());
        } else {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return $result;
    }

    private function getResponseModelSchemaByClass(string $class, bool $isNullable): RestApiBundle\DTO\Docs\Types\ObjectType
    {
        $class = ltrim($class, '\\');
        $cacheKey = sprintf('%s-%d', $class, $isNullable);

        if (isset($this->objectClassCache[$cacheKey])) {
            return $this->objectClassCache[$cacheKey];
        }

        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
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

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\Docs\Types\StringType(false);

        $this->objectClassCache[$cacheKey] = new RestApiBundle\DTO\Docs\Types\ObjectType($properties, $isNullable);

        return $this->objectClassCache[$cacheKey];
    }
}
