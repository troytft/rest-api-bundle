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
     * @var array<string, RestApiBundle\DTO\Docs\Type\ObjectType>
     */
    private $objectClassCache = [];

    public function getObjectTypeByClass(string $class, bool $isNullable): RestApiBundle\DTO\Docs\Type\ObjectType
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

            switch ((string) $returnType) {
                case 'string':
                    $properties[$propertyName] = new RestApiBundle\DTO\Docs\Type\StringType($returnType->allowsNull());

                    break;

                case 'int':
                case 'integer':
                    $properties[$propertyName] = new RestApiBundle\DTO\Docs\Type\IntegerType($returnType->allowsNull());

                    break;

                case 'float':
                    $properties[$propertyName] = new RestApiBundle\DTO\Docs\Type\FloatType($returnType->allowsNull());

                    break;

                case 'bool':
                case 'boolean':
                    $properties[$propertyName] = new RestApiBundle\DTO\Docs\Type\BooleanType($returnType->allowsNull());

                    break;

                default:
                    $class = (string) $returnType;
                    $properties[$propertyName] = $this->getObjectTypeByClass($class, $returnType->allowsNull());
            }
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\Docs\Type\StringType(false);

        $this->objectClassCache[$class] = new RestApiBundle\DTO\Docs\Type\ObjectType($properties, $isNullable);

        return $this->objectClassCache[$class];
    }
}
