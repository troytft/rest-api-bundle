<?php

namespace RestApiBundle\Services\OpenApi\Schema;

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

    public function __construct(private RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
    }

    public function resolveReference(string $class): OpenApi\Reference
    {
        $typename = $this->typenameCache[$class] ?? null;
        if (!$typename) {
            if (!RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($class)) {
                throw new \InvalidArgumentException(sprintf('Class %s is not a response model', $class));
            }

            $typename = RestApiBundle\Helper\ResponseModel\TypenameResolver::resolve($class);
            $classInCache = array_search($typename, $this->typenameCache, true);
            if ($classInCache !== false && $classInCache !== $class) {
                throw new \InvalidArgumentException(sprintf('Typename %s for class %s already used by another class %s', $typename, $class, $classInCache));
            }

            $this->typenameCache[$class] = $typename;
            $this->schemaCache[$class] = $this->resolveResponseModel($class, $typename);
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

    private function resolveResponseModel(string $class, string $typename): OpenApi\Schema
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
                $returnType = RestApiBundle\Helper\TypeExtractor::extractReturnType($reflectionMethod);
                if (!$returnType) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Return type is not specified', $reflectionMethod);
                }

                $propertySchema = $this->resolveByType($returnType);
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

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    private function resolveByType(PropertyInfo\Type $type)
    {
        switch (true) {
            case $type->isCollection():
                $result = $this->resolveCollection($type);

                break;

            case RestApiBundle\Helper\TypeExtractor::isScalar($type):
                $result = RestApiBundle\Helper\OpenApiHelper::createScalarFromPropertyInfoType($type);

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($type->getClassName()):
                $result = $this->resolveReference($type->getClassName());
                if ($type->isNullable()) {
                    $result = new OpenApi\Schema([
                        'anyOf' => [$result,],
                        'nullable' => true,
                    ]);
                }

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isDateTime($type->getClassName()):
                $format = $this->settingsProvider->getResponseModelDateTimeFormat();
                $result = RestApiBundle\Helper\OpenApiHelper::createDateTime($format, $type->isNullable());

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isResponseModelEnum($type->getClassName()):
                $result = $this->resolveEnum($type);

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isSerializableDate($type->getClassName()):
                $format = $this->settingsProvider->getResponseModelDateFormat();
                $result = RestApiBundle\Helper\OpenApiHelper::createDate($format, $type->isNullable());

                break;

            default:
                throw new RestApiBundle\Exception\OpenApi\ResponseModel\UnknownTypeException();
        }

        return $result;
    }

    private function resolveCollection(PropertyInfo\Type $type): OpenApi\Schema
    {
        if (!$type->getCollectionValueTypes()) {
            throw new RestApiBundle\Exception\OpenApi\ResponseModel\UnknownTypeException();
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $this->resolveByType(RestApiBundle\Helper\TypeExtractor::getFirstCollectionValueType($type)),
            'nullable' => $type->isNullable(),
        ]);
    }

    private function resolveEnum(PropertyInfo\Type $type): OpenApi\Schema
    {
        $enumValues = RestApiBundle\Helper\TypeExtractor::extractEnumValues($type->getClassName());

        if (is_int($enumValues[0])) {
            $result = new OpenApi\Schema([
                'type' => OpenApi\Type::INTEGER,
                'nullable' => $type->isNullable(),
                'enum' => $enumValues,
            ]);
        } elseif (is_string($enumValues[0])) {
            $result = new OpenApi\Schema([
                'type' => OpenApi\Type::STRING,
                'nullable' => $type->isNullable(),
                'enum' => $enumValues,
            ]);
        } else {
            throw new \LogicException();
        }

        return $result;
    }
}
