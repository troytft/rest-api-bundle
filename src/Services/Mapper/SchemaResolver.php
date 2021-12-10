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
            $typeOptions = [];
            $propertyAnnotations = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotations($reflectionProperty);

            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\Expose) {
                    $isExposed = true;
                } elseif ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\FindByField) {
                    $typeOptions[] = $propertyAnnotation;
                } elseif ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                    $typeOptions[] = $propertyAnnotation;
                }
            }

            if (!$isExposed && !$isExposedAll) {
                continue;
            }

            try {
                $mapping = $this->preprocessMapping($reflectionProperty, $typeOptions);
                $propertySchema = $this->resolveSchemaByMapping($mapping);

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

    private function resolveSchemaByMapping(RestApiBundle\Model\Mapper\Types\TypeInterface $mapping): RestApiBundle\Model\Mapper\Schema
    {
        if ($mapping instanceof RestApiBundle\Model\Mapper\Types\TransformerAwareTypeInterface) {
            $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(
                $mapping->getTransformerClass(),
                $mapping->getTransformerOptions(),
                $mapping->getIsNullable() ?? false
            );
        } elseif ($mapping instanceof RestApiBundle\Model\Mapper\Types\ModelType) {
            $schema = $this->resolve($mapping->getClass(), $mapping->getIsNullable() ?? false);
        } elseif ($mapping instanceof RestApiBundle\Model\Mapper\Types\ArrayType) {
            $valuesType = $mapping->getValuesType();
            if ($valuesType instanceof RestApiBundle\Model\Mapper\Types\TransformerAwareTypeInterface && $valuesType->getTransformerClass() === RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class) {
                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(
                    RestApiBundle\Services\Mapper\Transformer\ArrayOfDoctrineEntitiesTransformer::class,
                    $valuesType->getTransformerOptions(),
                    $mapping->getIsNullable() ?? false
                );
            } else {
                $schema = RestApiBundle\Model\Mapper\Schema::createArrayType($this->resolveSchemaByMapping($valuesType), $mapping->getIsNullable() ?? false);
            }
        } else {
            throw new \LogicException();
        }

        return $schema;
    }

    private function preprocessMapping(\ReflectionProperty $reflectionProperty, array $typeOptions = []): RestApiBundle\Model\Mapper\Types\TypeInterface
    {
        $type = RestApiBundle\Helper\PropertyInfoTypeHelper::extractPropertyType($reflectionProperty);

        if (!$type) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionPropertyAwareException('Property has empty type', $reflectionProperty);
        }

        return $this->resolveMappingByType($type, $typeOptions);
    }

    private function resolveMappingByType(PropertyInfo\Type $type, array $typeOptions = []): RestApiBundle\Model\Mapper\Types\TypeInterface
    {
        switch (true) {
            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING:
                $result = new RestApiBundle\Model\Mapper\Types\StringType(nullable: $type->isNullable());

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_INT:
                $result = new RestApiBundle\Model\Mapper\Types\IntegerType(nullable: $type->isNullable());

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_FLOAT:
                $result = new RestApiBundle\Model\Mapper\Types\FloatType(nullable: $type->isNullable());

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_BOOL:
                $result = new RestApiBundle\Model\Mapper\Types\BooleanType(nullable: $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isDateMapperType($type->getClassName()):
                $result = new RestApiBundle\Model\Mapper\Types\DateType(nullable: $type->isNullable());

                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                        $result->format = $typeOption->getFormat();
                    }
                }

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isDateTime($type->getClassName()):
                $result = new RestApiBundle\Model\Mapper\Types\DateTimeType(nullable: $type->isNullable());

                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                        $result->format = $typeOption->getFormat();
                    }
                }

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($type->getClassName()):
                $result = new RestApiBundle\Model\Mapper\Types\ModelType(class: (string) $type->getClassName(), nullable: $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($type->getClassName()):
                $result = new RestApiBundle\Model\Mapper\Types\DoctrineEntityType(class: (string) $type->getClassName(), nullable: $type->isNullable());

                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\FindByField) {
                        $result->field = $typeOption->getField();
                    }
                }

                break;

            case $type->isCollection():
                $valueType = $this->resolveMappingByType(RestApiBundle\Helper\PropertyInfoTypeHelper::getFirstCollectionValueType($type), $typeOptions);
                $result = new RestApiBundle\Model\Mapper\Types\ArrayType($valueType, nullable: $type->isNullable());

                break;

            default:
                throw new \LogicException();
        }

        return $result;
    }
}
