<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\PropertyInfo;

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

    private PropertyInfo\PropertyInfoExtractor $propertyInfoExtractor;

    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        private PropertyInfo\Extractor\ReflectionExtractor $reflectionExtractor,
        private PropertyInfo\Extractor\PhpDocExtractor $phpDocExtractor,
    ) {
        $this->propertyInfoExtractor = new PropertyInfo\PropertyInfoExtractor(
            [$this->reflectionExtractor],
            [$this->phpDocExtractor, $this->reflectionExtractor],
        );
    }

    public function resolveReference(string $class): OpenApi\Reference
    {
        $typename = $this->typenameCache[$class] ?? null;
        if (!$typename) {
            if (!RestApiBundle\Helper\ReflectionHelper::isResponseModel($class)) {
                throw new \InvalidArgumentException(sprintf('Class %s is not a response model', $class));
            }

            $typename = RestApiBundle\Helper\ResponseModel\TypenameResolver::resolve($class);
            $classInCache = array_search($typename, $this->typenameCache, true);
            if ($classInCache !== false && $classInCache !== $class) {
                throw new \InvalidArgumentException(sprintf('Typename %s for class %s already used by another class %s', $typename, $class, $classInCache));
            }

            $this->typenameCache[$class] = $typename;
            $this->schemaCache[$class] = $this->resolveModelSchema($class, $typename);
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


    private function resolveModelSchema(string $class, string $typename): OpenApi\Schema
    {
        $properties = [];

//        var_dump($this->propertyInfoExtractor->getProperties($class));
        $reflectedClass = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class);
        $reflectedMethods = $reflectedClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectedMethods as $reflectionMethod) {
            if (!str_starts_with($reflectionMethod->getName(), 'get')) {
                continue;
            }


            $propertyName = lcfirst(substr($reflectionMethod->getName(), 3));

//            var_dump($this->propertyInfoExtractor->getTypes($class, $propertyName));

            try {
                $returnType = RestApiBundle\Helper\TypeExtractor::extractByReflectionMethod($reflectionMethod);
                if (!$returnType) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Return type is not specified', $reflectionMethod);
                }

                $propertySchema = $this->resolveByType($returnType);

                if (RestApiBundle\Helper\ReflectionHelper::isDeprecated($reflectionMethod)) {
                    $propertySchema->deprecated = true;
                }
            } catch (RestApiBundle\Exception\OpenApi\ResponseModel\UnknownTypeException $exception) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException(sprintf('Unknown type: %s', $reflectionMethod->class), $reflectionMethod);
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
                $result = RestApiBundle\Helper\OpenApi\SchemaHelper::createScalarFromPropertyInfoType($type);

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ReflectionHelper::isResponseModel($type->getClassName()):
                $result = $this->resolveReference($type->getClassName());
                if ($type->isNullable()) {
                    $result = new OpenApi\Schema([
                        'anyOf' => [$result,],
                        'nullable' => true,
                    ]);
                }

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ReflectionHelper::isDateTime($type->getClassName()):
                $format = $this->settingsProvider->getResponseModelDateTimeFormat();
                $result = RestApiBundle\Helper\OpenApi\SchemaHelper::createDateTime($format, $type->isNullable());

                break;

            case $type->getClassName() && enum_exists($type->getClassName()):
            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ReflectionHelper::isResponseModelEnum($type->getClassName()):
                $result = RestApiBundle\Helper\OpenApi\SchemaHelper::createEnum($type->getClassName(), $type->isNullable());

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ReflectionHelper::isResponseModelDate($type->getClassName()):
                $format = $this->settingsProvider->getResponseModelDateFormat();
                $result = RestApiBundle\Helper\OpenApi\SchemaHelper::createDate($format, $type->isNullable());

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
            'items' => $this->resolveByType(RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($type)),
            'nullable' => $type->isNullable(),
        ]);
    }
}
