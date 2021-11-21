<?php

namespace RestApiBundle\Services\OpenApi\Specification;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\PropertyInfo;

use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function ksort;
use function lcfirst;
use function sprintf;
use function substr;

class ResponseModelResolver
{
    /**
     * @var array<string, OpenApi\Schema>
     */
    private array $schemaCache = [];

    /**
     * @var array<string, string>
     */
    private array $typenameCache = [];

    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
    ) {
    }

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    public function resolveReferenceByClass(string $class)
    {
        if (isset($this->typenameCache[$class])) {
            $typename = $this->typenameCache[$class];
        } else {
            if (!RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($class)) {
                throw new \InvalidArgumentException(sprintf('Class %s is not a response model.', $class));
            }

            $typename = RestApiBundle\Helper\ResponseModel\TypenameResolver::resolve($class);
            $classInCache = array_search($typename, $this->typenameCache, true);
            if ($classInCache !== false && $classInCache !== $class) {
                throw new \InvalidArgumentException(sprintf('Typename %s for class %s already used by another class %s', $typename, $class, $classInCache));
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
            if (!str_starts_with($reflectionMethod->getName(), 'get')) {
                continue;
            }

            $propertyName = lcfirst(substr($reflectionMethod->getName(), 3));

            try {
                $propertySchema = $this->convert($this->getReturnType($reflectionMethod));
            } catch (RestApiBundle\Exception\OpenApi\ResponseModel\UnknownTypeException $exception) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Unknown type', $reflectionMethod);
            }

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

    private function getReturnType(\ReflectionMethod $reflectionMethod): PropertyInfo\Type
    {
        $result = RestApiBundle\Helper\TypeExtractor::extractReturnType($reflectionMethod);
        if (!$result) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Return type not found in docBlock and type-hint', $reflectionMethod);
        }

        return $result;
    }

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    private function convert(PropertyInfo\Type $type)
    {
        if ($type->isCollection()) {
            $result = $this->convertArrayType($type);
        } elseif (RestApiBundle\Helper\TypeExtractor::isScalar($type)) {
            $result = RestApiBundle\Helper\OpenApiHelper::createScalarFromType($type);
        } elseif ($type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT) {
            $result = $this->convertClassType($type);
        } else {
            throw new RestApiBundle\Exception\OpenApi\ResponseModel\UnknownTypeException();
        }

        return $result;
    }

    private function convertArrayType(PropertyInfo\Type $arrayType): OpenApi\Schema
    {
        if (!$arrayType->getCollectionValueTypes()) {
            throw new RestApiBundle\Exception\OpenApi\ResponseModel\UnknownTypeException();
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $this->convert(RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($arrayType)),
            'nullable' => $arrayType->isNullable(),
        ]);
    }

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    private function convertClassType(PropertyInfo\Type $classType)
    {
        switch (true) {
            case RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($classType->getClassName()):
                $result = $this->resolveReferenceByClass($classType->getClassName());
                if ($classType->isNullable()) {
                    $result = new OpenApi\Schema([
                        'anyOf' => [$result,],
                        'nullable' => true,
                    ]);
                }

                break;

            case RestApiBundle\Helper\ClassInstanceHelper::isDateTime($classType->getClassName()):
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date-time',
                    'example' => RestApiBundle\Helper\OpenApiHelper::getExampleDate()->format($this->settingsProvider->getResponseModelDateTimeFormat()),
                    'nullable' => $classType->isNullable(),
                ]);

                break;

            case RestApiBundle\Helper\ClassInstanceHelper::isSerializableEnum($classType->getClassName()):
                $result = $this->convertSerializableEnum($classType);

                break;

            case RestApiBundle\Helper\ClassInstanceHelper::isSerializableDate($classType->getClassName()):
                $result = $this->convertSerializableDate($classType);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported class type %s', $classType->getClassName()));
        }

        return $result;
    }

    private function convertSerializableEnum(PropertyInfo\Type $classType): OpenApi\Schema
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($classType->getClassName());

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
                'nullable' => $classType->isNullable(),
                'enum' => $values,
            ]);
        } elseif (is_int($values[0])) {
            $result = new OpenApi\Schema([
                'type' => OpenApi\Type::INTEGER,
                'nullable' => $classType->isNullable(),
                'enum' => $values,
            ]);
        } elseif (is_string($values[0])) {
            $result = new OpenApi\Schema([
                'type' => OpenApi\Type::STRING,
                'nullable' => $classType->isNullable(),
                'enum' => $values,
            ]);
        } else {
            throw new \LogicException();
        }

        return $result;
    }

    private function convertSerializableDate(PropertyInfo\Type $classType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'format' => 'date',
            'example' => RestApiBundle\Helper\OpenApiHelper::getExampleDate()->format($this->settingsProvider->getResponseModelDateFormat()),
            'nullable' => $classType->isNullable(),
        ]);
    }
}
