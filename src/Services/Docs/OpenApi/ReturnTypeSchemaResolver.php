<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use function lcfirst;
use function strpos;
use function substr;

class ReturnTypeSchemaResolver
{
    public function resolve(RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface $returnType): OpenApi\Schema
    {
        if (!$returnType instanceof RestApiBundle\DTO\Docs\ReturnType\ClassType) {
            throw new \InvalidArgumentException('Not implemented.');
        }

        $reflectionClass = new \ReflectionClass($returnType->getClass());

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

            $returnType = (string) $reflectionClass->getMethod($reflectionMethod->getName())->getReturnType();

            switch ($returnType) {
                case 'int':
                    $properties[$propertyName] = [
                        'type' => 'number',
                    ];

                    break;

                case 'string':
                    $properties[$propertyName] = [
                        'type' => 'string',
                    ];

                    break;

                default:
                    throw new \InvalidArgumentException('Not implemented.');
            }
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = [
            'type' => 'string',
        ];

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'properties' => $properties,
        ]);
    }
}
