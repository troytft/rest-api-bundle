<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;

use function ksort;
use function lcfirst;
use function sprintf;
use function strpos;
use function substr;

class ResponseModelResolver extends RestApiBundle\Services\Docs\OpenApi\AbstractSchemaResolver
{
    /**
     * @var array<string, OpenApi\Schema>
     */
    private $schemaCache = [];

    /**
     * @var array<string, string>
     */
    private $typenameCache = [];

    /**
     * @var RestApiBundle\Services\Docs\Types\TypeHintTypeReader
     */
    private $typeHintReader;

    /**
     * @var RestApiBundle\Services\Docs\Types\DocBlockTypeReader
     */
    private $docBlockReader;

    /**
     * @var RestApiBundle\Services\Response\ResponseModelTypenameResolver
     */
    private $typenameResolver;

    public function __construct(
        RestApiBundle\Services\Docs\Types\TypeHintTypeReader $typeHintReader,
        RestApiBundle\Services\Docs\Types\DocBlockTypeReader $docBlockReader,
        RestApiBundle\Services\Response\ResponseModelTypenameResolver $typenameResolver
    ) {
        $this->typeHintReader = $typeHintReader;
        $this->docBlockReader = $docBlockReader;
        $this->typenameResolver = $typenameResolver;
    }

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    public function resolveReferenceByClass(string $class)
    {
        if (isset($this->typenameCache[$class])) {
            $typename = $this->typenameCache[$class];
        } else {
            if (!RestApiBundle\Helper\ClassInterfaceChecker::isResponseModel($class)) {
                throw new \InvalidArgumentException(sprintf('Class %s is not a response model.', $class));
            }

            $typename = $this->typenameResolver->resolve($class);
            if (isset($this->typenameCache[$typename])) {
                throw new \InvalidArgumentException(sprintf('Typename %s for class %s already used by another class %s', $typename, $class, $this->typenameCache[$typename]));
            }

            $this->typenameCache[$class] = $typename;
            $this->schemaCache[$class] = $this->resolveSchema($class);
        }

        return new OpenApi\Reference([
            '$ref' => sprintf('#/components/schemas/%s', $typename),
        ]);
    }

    /**
     * @return array<string, OpenApi\Schema>
     */
    public function dumpSchemas(): array
    {
        $result = [];

        foreach ($this->typenameCache as $class => $typename) {
            $result[$typename] = $this->schemaCache[$class];
        }

        ksort($result);

        return $result;
    }

    private function resolveSchema(string $class): OpenApi\Schema
    {
        $properties = [];

        $reflectedClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $reflectedMethods = $reflectedClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectedMethods as $reflectionMethod) {
            if (strpos($reflectionMethod->getName(), 'get') !== 0) {
                continue;
            }

            $propertyName = lcfirst(substr($reflectionMethod->getName(), 3));
            $propertySchema = $this->convert($this->getReturnType($reflectionMethod));

            $properties[$propertyName] = $propertySchema;
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => false,
        ]);

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'properties' => $properties,
        ]);
    }

    private function getReturnType(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        $result = $this->docBlockReader->resolveReturnType($reflectionMethod) ?: $this->typeHintReader->resolveReturnType($reflectionMethod);
        if (!$result) {
            $context = sprintf('%s::%s', $reflectionMethod->class, $reflectionMethod->name);
            throw new RestApiBundle\Exception\Docs\InvalidDefinitionException(new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException(), $context);
        }

        return $result;
    }

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    private function convert(RestApiBundle\DTO\Docs\Types\TypeInterface $type)
    {
        if ($type instanceof RestApiBundle\DTO\Docs\Types\ArrayType) {
            $result = $this->convertArrayType($type);
        } elseif ($type instanceof RestApiBundle\DTO\Docs\Types\ScalarInterface) {
            $result = $this->resolveScalarType($type);
        } elseif ($type instanceof RestApiBundle\DTO\Docs\Types\ClassType) {
            $result = $this->convertClassType($type);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertArrayType(RestApiBundle\DTO\Docs\Types\ArrayType $arrayType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $this->convert($arrayType->getInnerType()),
            'nullable' => $arrayType->getNullable(),
        ]);
    }

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    private function convertClassType(RestApiBundle\DTO\Docs\Types\ClassType $classType)
    {
        switch (true) {
            case RestApiBundle\Helper\ClassInterfaceChecker::isResponseModel($classType->getClass()):
                $result = $this->resolveReferenceByClass($classType->getClass());
                if ($classType->getNullable()) {
                    $result = new OpenApi\Schema([
                        'anyOf' => [$result,],
                        'nullable' => true,
                    ]);
                }

                break;

            case RestApiBundle\Helper\ClassInterfaceChecker::isDateTime($classType->getClass()):
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
