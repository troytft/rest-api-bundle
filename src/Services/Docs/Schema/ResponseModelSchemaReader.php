<?php

namespace RestApiBundle\Services\Docs\Schema;

use RestApiBundle;
use function lcfirst;
use function ltrim;
use function strpos;
use function substr;

class ResponseModelSchemaReader
{
    /**
     * @var array<string, RestApiBundle\DTO\Docs\Schema\ObjectType>
     */
    private $objectClassCache = [];

    public function getSchemaByClassType(RestApiBundle\DTO\Docs\Schema\ClassType $classType): RestApiBundle\DTO\Docs\Schema\ObjectType
    {
        return $this->getSchemaByClass($classType->getClass(), $classType->getNullable());
    }

    public function getSchemaByClass(string $class, bool $isNullable): RestApiBundle\DTO\Docs\Schema\ObjectType
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
            $returnType = $reflectionClass->getMethod($reflectionMethod->getName())->getReturnType();
            $properties[$propertyName] = $this->convertReflectionTypeToSchemaType($returnType);
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\Docs\Schema\StringType(false);

        $this->objectClassCache[$class] = new RestApiBundle\DTO\Docs\Schema\ObjectType($properties, $isNullable);

        return $this->objectClassCache[$class];
    }

    public function getSchemaByArrayOfClassesType(RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType $arrayOfClassesType): RestApiBundle\DTO\Docs\Schema\ArrayType
    {
        $objectType = $this->getSchemaByClassType(new RestApiBundle\DTO\Docs\Schema\ClassType($arrayOfClassesType->getClass(), $arrayOfClassesType->getNullable()));

        return new RestApiBundle\DTO\Docs\Schema\ArrayType($objectType, $arrayOfClassesType->getNullable());
    }

    private function convertReflectionTypeToSchemaType(\ReflectionType $reflectionType): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $typeAsString = (string) $reflectionType;

        switch ($typeAsString) {
            case 'string':
                $result = new RestApiBundle\DTO\Docs\Schema\StringType($reflectionType->allowsNull());

                break;

            case 'int':
            case 'integer':
                $result = new RestApiBundle\DTO\Docs\Schema\IntegerType($reflectionType->allowsNull());

                break;

            case 'float':
                $result = new RestApiBundle\DTO\Docs\Schema\FloatType($reflectionType->allowsNull());

                break;

            case 'bool':
            case 'boolean':
                $result = new RestApiBundle\DTO\Docs\Schema\BooleanType($reflectionType->allowsNull());

                break;

            case \DateTime::class:
                $result = new RestApiBundle\DTO\Docs\Schema\DateTimeType($reflectionType->allowsNull());

                break;

            default:
                $result = $this->getSchemaByClassType(new RestApiBundle\DTO\Docs\Schema\ClassType($typeAsString, $reflectionType->allowsNull()));
        }

        return $result;
    }
}
