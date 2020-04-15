<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use function lcfirst;
use function ltrim;
use function strpos;
use function substr;

class ResponseModelHelper
{
    /**
     * @var array<string, RestApiBundle\DTO\Docs\Schema\ObjectType>
     */
    private $objectClassCache = [];

    /**
     * @var RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader
     */
    private $typeHintSchemaReader;

    /**
     * @var RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader
     */
    private $docBlockSchemaReader;

    public function __construct(
        RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader $typeHintSchemaReader,
        RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader $docBlockSchemaReader
    ) {
        $this->typeHintSchemaReader = $typeHintSchemaReader;
        $this->docBlockSchemaReader = $docBlockSchemaReader;
    }

    private function assertIsResponseModel(\ReflectionClass $reflectionClass): void
    {
        if (!$reflectionClass->implementsInterface(RestApiBundle\ResponseModelInterface::class)) {
            throw new \InvalidArgumentException();
        }
    }

    public function getSchemaByClass(string $class, bool $isNullable): RestApiBundle\DTO\Docs\Schema\ObjectType
    {
        $class = ltrim($class, '\\');

        if (isset($this->objectClassCache[$class])) {
            return $this->objectClassCache[$class];
        }

        $reflectionClass = RestApiBundle\Services\ReflectionClassStore::get($class);
        $this->assertIsResponseModel($reflectionClass);

        $properties = [];
        $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectionMethods as $reflectionMethod) {
            if (strpos($reflectionMethod->getName(), 'get') !== 0) {
                continue;
            }

            $propertyName = lcfirst(substr($reflectionMethod->getName(), 3));
            $properties[$propertyName] = $this->getReturnTypeByReflectionMethod($reflectionMethod);
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\Docs\Schema\StringType(false);

        $this->objectClassCache[$class] = new RestApiBundle\DTO\Docs\Schema\ObjectType($properties, $isNullable);

        return $this->objectClassCache[$class];
    }

    private function getReturnTypeByReflectionMethod(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $returnType = $this->typeHintSchemaReader->getMethodReturnSchema($reflectionMethod) ?? $this->docBlockSchemaReader->getMethodReturnSchema($reflectionMethod);
        if (!$returnType) {
            throw new \InvalidArgumentException('Empty return type.'); // @todo: make more informative
        }

        if ($returnType instanceof RestApiBundle\DTO\Docs\Schema\ClassType) {
            $returnType = $this->getSchemaByClass($returnType->getClass(), $returnType->getNullable());
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\Schema\ArrayType) {
            if (!$returnType->getInnerType() instanceof RestApiBundle\DTO\Docs\Schema\ClassType) {
                throw new \InvalidArgumentException();
            }

            $innerSchema = $this->getSchemaByClass($returnType->getInnerType()->getClass(), $returnType->getInnerType()->getNullable());
            $returnType = new RestApiBundle\DTO\Docs\Schema\ArrayType($innerSchema, $returnType->getNullable());
        }

        return $returnType;
    }
}
