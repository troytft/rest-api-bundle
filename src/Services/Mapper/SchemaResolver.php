<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

use function sprintf;
use function ucfirst;

class SchemaResolver implements RestApiBundle\Services\Mapper\SchemaResolverInterface
{
    public function resolve(string $class, bool $isNullable = false): RestApiBundle\Model\Mapper\Schema
    {
        $properties = [];
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $isExposedAll = RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionClass, RestApiBundle\Mapping\Mapper\ExposeAll::class) instanceof RestApiBundle\Mapping\Mapper\ExposeAll;

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $isExposed = false;
            $propertyOptions = [];
            $propertyAnnotations = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotations($reflectionProperty);

            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\Expose) {
                    $isExposed = true;
                } elseif ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\FindByField) {
                    $propertyOptions[] = $propertyAnnotation;
                } elseif ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                    $propertyOptions[] = $propertyAnnotation;
                }
            }

            if (!$isExposed && !$isExposedAll) {
                continue;
            }

            try {
                $reflectionPropertyType = RestApiBundle\Helper\PropertyInfoTypeHelper::extractPropertyType($reflectionProperty);
                if (!$reflectionPropertyType) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionPropertyAwareException('Property has empty type', $reflectionProperty);
                }

                $propertySchema = $this->resolveSchemaByType($reflectionPropertyType, $propertyOptions);
                $propertySetterName = 'set' . ucfirst($reflectionProperty->getName());

                if ($reflectionClass->hasMethod($propertySetterName) && $reflectionClass->getMethod($propertySetterName)->isPublic()) {
                    $propertySchema->propertySetterName = $propertySetterName;
                } elseif (!$reflectionProperty->isPublic()) {
                    throw new RestApiBundle\Exception\Mapper\Schema\InvalidDefinitionException(sprintf('Setter with name "%s" does not exist.', $propertySetterName));
                }
            } catch (RestApiBundle\Exception\Mapper\Schema\InvalidDefinitionException $exception) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionPropertyAwareException($exception->getMessage(), $reflectionProperty, $exception);
            }

            $properties[$reflectionProperty->getName()] = $propertySchema;
        }

        return RestApiBundle\Model\Mapper\Schema::createModelType($class, $properties, $isNullable);
    }

    private function resolveSchemaByType(PropertyInfo\Type $type, array $typeOptions = []): RestApiBundle\Model\Mapper\Schema
    {
        switch (true) {
            case RestApiBundle\Helper\PropertyInfoTypeHelper::isScalar($type):
                $schema  = match ($type->getBuiltinType()) {
                    PropertyInfo\Type::BUILTIN_TYPE_STRING => RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, [], $type->isNullable()),
                    PropertyInfo\Type::BUILTIN_TYPE_INT => RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, [], $type->isNullable()),
                    PropertyInfo\Type::BUILTIN_TYPE_FLOAT => RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, [], $type->isNullable()),
                    PropertyInfo\Type::BUILTIN_TYPE_BOOL => RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, [], $type->isNullable()),
                    default => throw new \LogicException(),
                };

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isDateMapperType($type->getClassName()):
                $dateFormat = null;
                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                        $dateFormat = $typeOption->getFormat();
                    }
                }

                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(RestApiBundle\Services\Mapper\Transformer\DateTransformer::class, [
                    RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION => $dateFormat,
                ], $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isDateTime($type->getClassName()):
                $dateFormat = null;
                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                        $dateFormat = $typeOption->getFormat();
                    }
                }

                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, [
                    RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION => $dateFormat,
                ], $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($type->getClassName()):
                $schema = $this->resolve($type->getClassName(), $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($type->getClassName()):
                $fieldName = 'id';
                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\FindByField) {
                        $fieldName = $typeOption->getField();
                    }
                }

                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class, [
                    RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::CLASS_OPTION => $type->getClassName(),
                    RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::FIELD_OPTION => $fieldName,
                ], $type->isNullable());

                break;

            case $type->isCollection():
                $collectionValueSchema = $this->resolveSchemaByType(RestApiBundle\Helper\PropertyInfoTypeHelper::getFirstCollectionValueType($type), $typeOptions);
                if ($collectionValueSchema->transformerClass === RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class) {
                    $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(RestApiBundle\Services\Mapper\Transformer\ArrayOfDoctrineEntitiesTransformer::class, $collectionValueSchema->transformerOptions, $type->isNullable());
                } else {
                    $schema = RestApiBundle\Model\Mapper\Schema::createArrayType($collectionValueSchema, $type->isNullable());
                }

                break;

            default:
                throw new \LogicException();
        }

        return $schema;
    }
}
