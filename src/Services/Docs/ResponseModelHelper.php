<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use function lcfirst;
use function strpos;
use function substr;

class ResponseModelHelper
{
    public function extractReturnTypeObjectFromResponseModelClass(string $class): RestApiBundle\DTO\Docs\ReturnType\ObjectType
    {
        $reflectionClass = new \ReflectionClass($class);
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
                case 'int':
                    $properties[$propertyName] = new RestApiBundle\DTO\Docs\ReturnType\IntegerType($returnType->allowsNull());

                    break;

                case 'string':
                    $properties[$propertyName] = new RestApiBundle\DTO\Docs\ReturnType\StringType($returnType->allowsNull());

                    break;

                default:
                    throw new \InvalidArgumentException('Not implemented.');
            }
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new RestApiBundle\DTO\Docs\ReturnType\StringType(false);

        return new RestApiBundle\DTO\Docs\ReturnType\ObjectType($properties, false);
    }
}
