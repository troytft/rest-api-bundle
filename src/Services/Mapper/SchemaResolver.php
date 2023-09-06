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
        $reflectionClass = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class);
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
                } elseif ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\Trim) {
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
                        throw new RestApiBundle\Exception\Mapper\Schema\InvalidDefinitionException(sprintf('Property "%s" must be public or setter must exist.', $reflectionProperty->getName()));
                    }

                    $propertySchema->propertyGetterName = 'get' . $formattedPropertyName;
                    if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                        $propertySchema->propertyGetterName = $reflectionProperty->getName();
                        if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                            $propertySchema->propertyGetterName = 'is' . $formattedPropertyName;
                            if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                                throw new RestApiBundle\Exception\Mapper\Schema\InvalidDefinitionException(sprintf('Property "%s" must be public or getter must exist.', $reflectionProperty->getName()));
                            }
                        }
                    }
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
            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING:
                $trim = null;
                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\Trim) {
                        $trim = true;
                    }
                }

                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $type->isNullable(), [
                    RestApiBundle\Services\Mapper\Transformer\StringTransformer::TRIM_OPTION => $trim,
                ]);

                break;

            case RestApiBundle\Helper\TypeExtractor::isScalar($type):
                $schema  = match ($type->getBuiltinType()) {
                    PropertyInfo\Type::BUILTIN_TYPE_INT => RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $type->isNullable()),
                    PropertyInfo\Type::BUILTIN_TYPE_FLOAT => RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $type->isNullable()),
                    PropertyInfo\Type::BUILTIN_TYPE_BOOL => RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $type->isNullable()),
                    default => throw new \LogicException(),
                };

                break;

            case $type->getClassName() && RestApiBundle\Helper\ReflectionHelper::isMapperDate($type->getClassName()):
                $dateFormat = null;
                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                        $dateFormat = $typeOption->getFormat();
                    }
                }

                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\DateTransformer::class, $type->isNullable(), [
                    RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION => $dateFormat,
                ]);

                break;

            case $type->getClassName() && RestApiBundle\Helper\ReflectionHelper::isDateTime($type->getClassName()):
                $dateFormat = null;
                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                        $dateFormat = $typeOption->getFormat();
                    }
                }

                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $type->isNullable(), [
                    RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION => $dateFormat,
                ]);

                break;

            case $type->getClassName() && RestApiBundle\Helper\ReflectionHelper::isMapperModel($type->getClassName()):
                $schema = $this->resolve($type->getClassName(), $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\ReflectionHelper::isMapperEnum($type->getClassName()):
                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\EnumTransformer::class, $type->isNullable(), [
                    RestApiBundle\Services\Mapper\Transformer\EnumTransformer::CLASS_OPTION => $type->getClassName(),
                ]);

                break;

            case $type->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($type->getClassName()):
                $fieldName = 'id';
                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\FindByField) {
                        $fieldName = $typeOption->getField();
                    }
                }

                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class, $type->isNullable(), [
                    RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::CLASS_OPTION => $type->getClassName(),
                    RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::FIELD_OPTION => $fieldName,
                ]);

                break;

            case $type->isCollection():
                $collectionValueSchema = $this->resolveSchemaByType(RestApiBundle\Helper\TypeExtractor::extractFirstCollectionValueType($type), $typeOptions);
                if ($collectionValueSchema->transformerClass === RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class) {
                    $schema = RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class, $type->isNullable(), array_merge($collectionValueSchema->transformerOptions, [
                       RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::MULTIPLE_OPTION => true,
                    ]));
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
