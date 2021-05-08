<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;

use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function ksort;
use function lcfirst;
use function sprintf;
use function strpos;
use function substr;

class ResponseModelResolver extends RestApiBundle\Services\OpenApi\AbstractSchemaResolver
{
    /**
     * @var array<string, OpenApi\Schema>
     */
    private array $schemaCache = [];

    /**
     * @var array<string, string>
     */
    private array $typenameCache = [];

    private RestApiBundle\Services\OpenApi\Types\TypeHintTypeReader $typeHintReader;
    private RestApiBundle\Services\OpenApi\Types\DocBlockTypeReader $docBlockReader;
    private RestApiBundle\Services\ResponseModel\TypenameResolver $typenameResolver;
    private RestApiBundle\Services\SettingsProvider $settingsProvider;

    public function __construct(
        RestApiBundle\Services\OpenApi\Types\TypeHintTypeReader $typeHintReader,
        RestApiBundle\Services\OpenApi\Types\DocBlockTypeReader $docBlockReader,
        RestApiBundle\Services\ResponseModel\TypenameResolver $typenameResolver,
        RestApiBundle\Services\SettingsProvider $settingsProvider
    ) {
        $this->typeHintReader = $typeHintReader;
        $this->docBlockReader = $docBlockReader;
        $this->typenameResolver = $typenameResolver;
        $this->settingsProvider = $settingsProvider;
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
            $this->schemaCache[$class] = $this->resolveSchema($class, $typename);
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

    private function resolveSchema(string $class, string $typename): OpenApi\Schema
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

        $properties[RestApiBundle\Services\ResponseModel\ResponseModelNormalizer::ATTRIBUTE_TYPENAME] = new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => false,
            'default' => $typename,
        ]);

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'properties' => $properties,
        ]);
    }

    private function getReturnType(\ReflectionMethod $reflectionMethod): RestApiBundle\Model\OpenApi\Types\TypeInterface
    {
        $result = $this->docBlockReader->resolveReturnType($reflectionMethod) ?: $this->typeHintReader->resolveReturnType($reflectionMethod);
        if (!$result) {
            $context = sprintf('%s::%s', $reflectionMethod->class, $reflectionMethod->name);
            throw new RestApiBundle\Exception\OpenApi\InvalidDefinitionException(new RestApiBundle\Exception\OpenApi\InvalidDefinition\EmptyReturnTypeException(), $context);
        }

        return $result;
    }

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    private function convert(RestApiBundle\Model\OpenApi\Types\TypeInterface $type)
    {
        if ($type instanceof RestApiBundle\Model\OpenApi\Types\ArrayType) {
            $result = $this->convertArrayType($type);
        } elseif ($type instanceof RestApiBundle\Model\OpenApi\Types\ScalarInterface) {
            $result = $this->resolveScalarType($type);
        } elseif ($type instanceof RestApiBundle\Model\OpenApi\Types\ClassType) {
            $result = $this->convertClassType($type);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertArrayType(RestApiBundle\Model\OpenApi\Types\ArrayType $arrayType): OpenApi\Schema
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
    private function convertClassType(RestApiBundle\Model\OpenApi\Types\ClassType $classType)
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
                    'example' => RestApiBundle\Helper\OpenApi\ExampleHelper::getExampleDate()->format($this->settingsProvider->getResponseModelDateTimeFormat()),
                    'nullable' => $classType->getNullable(),
                ]);

                break;

            case RestApiBundle\Helper\ClassInterfaceChecker::isSerializableEnum($classType->getClass()):
                $result = $this->convertSerializableEnum($classType);

                break;

            case RestApiBundle\Helper\ClassInterfaceChecker::isSerializableDate($classType->getClass()):
                $result = $this->convertSerializableDate($classType);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported class type %s', $classType->getClass()));
        }

        return $result;
    }

    private function convertSerializableEnum(RestApiBundle\Model\OpenApi\Types\ClassType $classType): OpenApi\Schema
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($classType->getClass());

        $values = [];
        foreach ($reflectionClass->getReflectionConstants() as $constant) {
            if (!$constant->isPublic() || !is_scalar($constant->getValue())) {
                continue;
            }

            $values[] = $constant->getValue();
        }

        if (!$values) {
            throw new \LogicException('Empty enum');
        }

        if (is_float($values[0])) {
            $result = new OpenApi\Schema([
                'type' => OpenApi\Type::NUMBER,
                'format' => 'double',
                'nullable' => $classType->getNullable(),
                'enum' => $values,
            ]);
        } elseif (is_int($values[0])) {
            $result = new OpenApi\Schema([
                'type' => OpenApi\Type::INTEGER,
                'nullable' => $classType->getNullable(),
                'enum' => $values,
            ]);
        } elseif (is_string($values[0])) {
            $result = new OpenApi\Schema([
                'type' => OpenApi\Type::STRING,
                'nullable' => $classType->getNullable(),
                'enum' => $values,
            ]);
        } else {
            throw new \LogicException();
        }

        return $result;
    }

    private function convertSerializableDate(RestApiBundle\Model\OpenApi\Types\ClassType $classType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'format' => 'date',
            'example' => RestApiBundle\Helper\OpenApi\ExampleHelper::getExampleDate()->format($this->settingsProvider->getResponseModelDateFormat()),
            'nullable' => $classType->getNullable(),
        ]);
    }
}
