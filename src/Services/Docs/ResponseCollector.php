<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use function lcfirst;
use function ltrim;
use function strpos;
use function substr;

class ResponseCollector
{
    /**
     * @var array<string, RestApiBundle\DTO\Docs\Schema\ObjectType>
     */
    private $objectClassCache = [];

    /**
     * @var RestApiBundle\Services\Docs\Schema\TypeHintReader
     */
    private $typeHintReader;

    /**
     * @var RestApiBundle\Services\Docs\Schema\DocBlockReader
     */
    private $docBlockReader;

    public function __construct(
        RestApiBundle\Services\Docs\Schema\TypeHintReader $typeHintReader,
        RestApiBundle\Services\Docs\Schema\DocBlockReader $docBlockReader
    ) {
        $this->typeHintReader = $typeHintReader;
        $this->docBlockReader = $docBlockReader;
    }

    public function getByReflectionMethod(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
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

        if (isset($this->objectClassCache[$class])) {
            return $this->objectClassCache[$class];
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

        $this->objectClassCache[$class] = new RestApiBundle\DTO\Docs\Schema\ObjectType($properties, $isNullable);

        return $this->objectClassCache[$class];
    }
}
