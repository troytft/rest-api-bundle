<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

use function sprintf;
use function ucfirst;

class SchemaResolver implements RestApiBundle\Services\Mapper\SchemaResolverInterface
{
    private array $schemaTypeResolvers;

    public function __construct()
    {
        $this->schemaTypeResolvers = [
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\StringTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\IntegerTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\FloatTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\BooleanTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\PhpEnumTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\PolyfillEnumTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\DoctrineEntityTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\DoctrineEntityArrayTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\DateTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\DateTimeTypeResolver(),
            new RestApiBundle\Services\Mapper\SchemaTypeResolver\UploadedFileTypeResolver(),
        ];
    }

    public function resolve(string $class, bool $isNullable = false): RestApiBundle\Model\Mapper\Schema
    {
        $properties = [];
        $reflectionClass = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class);
        $isExposedAll = RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionClass, RestApiBundle\Mapping\Mapper\ExposeAll::class) instanceof RestApiBundle\Mapping\Mapper\ExposeAll;

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $isExposed = false;
            $propertyOptions = [];
            $propertyAnnotations = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotations($reflectionProperty);

            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\Expose) {
                    $isExposed = true;
                } elseif ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\PropertyOptionInterface) {
                    $propertyOptions[] = $propertyAnnotation;
                }
            }

            if (!$isExposed && !$isExposedAll) {
                continue;
            }

            try {
                $reflectionPropertyType = RestApiBundle\Helper\TypeExtractor::extractByReflectionProperty($reflectionProperty);
                if (!$reflectionPropertyType) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionPropertyAwareException('Property has empty type', $reflectionProperty);
                }

                $propertySchema = $this->resolveSchemaByType($reflectionPropertyType, $propertyOptions);

                if (!$reflectionProperty->isPublic()) {
                    $formattedPropertyName = ucfirst($reflectionProperty->getName());
                    $propertySchema->propertySetterName = 'set' . $formattedPropertyName;
                    if (!$reflectionClass->hasMethod($propertySchema->propertySetterName) || !$reflectionClass->getMethod($propertySchema->propertySetterName)->isPublic()) {
                        throw new RestApiBundle\Exception\Schema\InvalidDefinitionException(sprintf('Property "%s" must be public or setter must exist.', $reflectionProperty->getName()));
                    }

                    $propertySchema->propertyGetterName = 'get' . $formattedPropertyName;
                    if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                        $propertySchema->propertyGetterName = $reflectionProperty->getName();
                        if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                            $propertySchema->propertyGetterName = 'is' . $formattedPropertyName;
                            if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                                throw new RestApiBundle\Exception\Schema\InvalidDefinitionException(sprintf('Property "%s" must be public or getter must exist.', $reflectionProperty->getName()));
                            }
                        }
                    }
                }
            } catch (RestApiBundle\Exception\Schema\InvalidDefinitionException $exception) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionPropertyAwareException($exception->getMessage(), $reflectionProperty, $exception);
            }

            $properties[$reflectionProperty->getName()] = $propertySchema;
        }

        return RestApiBundle\Model\Mapper\Schema::createModelType($class, $properties, $isNullable);
    }

    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    private function resolveSchemaByType(PropertyInfo\Type $propertyInfoType, array $typeOptions = []): RestApiBundle\Model\Mapper\Schema
    {
        $schema = null;
        foreach ($this->schemaTypeResolvers as $schemaTypeResolver) {
            if ($schemaTypeResolver->supports($propertyInfoType, $typeOptions)) {
                $schema = $schemaTypeResolver->resolve($propertyInfoType, $typeOptions);

                break;
            }
        }

        if (!$schema) {
            if ($propertyInfoType->getClassName() && RestApiBundle\Helper\ReflectionHelper::isMapperModel($propertyInfoType->getClassName())) {
                $schema = $this->resolve($propertyInfoType->getClassName(), $propertyInfoType->isNullable());
            } elseif ($propertyInfoType->isCollection()) {
                $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($propertyInfoType);
                $collectionValueSchema = $this->resolveSchemaByType($collectionValueType, $typeOptions);
                $schema = RestApiBundle\Model\Mapper\Schema::createArrayType($collectionValueSchema, $propertyInfoType->isNullable());
            } else {
                throw new \LogicException(sprintf('Unknown type: %s', $this->propertyTypeToString($propertyInfoType)));
            }
        }

        return $schema;
    }

    private function propertyTypeToString(PropertyInfo\Type $propertyInfoType): string
    {
        $prefix = $propertyInfoType->isNullable() ? '?' : '';
        $builtinType = $propertyInfoType->getBuiltinType();

        if ($builtinType === PropertyInfo\Type::BUILTIN_TYPE_OBJECT) {
            $className = $propertyInfoType->getClassName();
            if ($className !== null) {
                return $prefix . $className;
            }
            return $prefix . 'object';
        }

        if ($builtinType === PropertyInfo\Type::BUILTIN_TYPE_ARRAY) {
            $collectionKeyType = $propertyInfoType->getCollectionKeyTypes();
            $collectionValueType = $propertyInfoType->getCollectionValueTypes();

            $keyType = $collectionKeyType ? $this->propertyTypeToString($collectionKeyType[0]) : 'mixed';
            $valueType = $collectionValueType ? $this->propertyTypeToString($collectionValueType[0]) : 'mixed';

            return $prefix . "array<{$keyType}, {$valueType}>";
        }

        return $prefix . $builtinType;
    }
}
