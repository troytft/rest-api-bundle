<?php

namespace RestApiBundle\Services\Docs\Type\Adapter;

use RestApiBundle;
use function lcfirst;
use function ltrim;
use function strpos;
use function substr;

class ResponseModelReader
{
    /**
     * @var array<string, RestApiBundle\DTO\Docs\Schema\ObjectType>
     */
    private $objectClassCache = [];

    public function resolveObjectTypeByClassType(RestApiBundle\DTO\Docs\Schema\ClassType $classType): RestApiBundle\DTO\Docs\Schema\ObjectType
    {
        return $this->getTypeByClass($classType->getClass(), $classType->getNullable());
    }

    public function getTypeByClass(string $class, bool $isNullable): RestApiBundle\DTO\Docs\Schema\ObjectType
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
            $properties[$propertyName] = $this->resolveTypeByReflectionType($returnType);
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\Docs\Schema\StringType(false);

        $this->objectClassCache[$class] = new RestApiBundle\DTO\Docs\Schema\ObjectType($properties, $isNullable);

        return $this->objectClassCache[$class];
    }

    public function resolveCollectionTypeByClassesCollectionType(RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType $classesCollectionType): RestApiBundle\DTO\Docs\Schema\ArrayType
    {
        $objectType = $this->resolveObjectTypeByClassType(new RestApiBundle\DTO\Docs\Schema\ClassType($classesCollectionType->getClass(), $classesCollectionType->getNullable()));

        return  new RestApiBundle\DTO\Docs\Schema\ArrayType($objectType, $classesCollectionType->getNullable());
    }

    private function resolveTypeByReflectionType(\ReflectionType $reflectionType): RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        switch ((string) $reflectionType) {
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

            default:
                $class = (string) $reflectionType;
                $result = $this->resolveObjectTypeByClassType(new RestApiBundle\DTO\Docs\Schema\ClassType($class, $reflectionType->allowsNull()));
        }

        return $result;
    }
}
