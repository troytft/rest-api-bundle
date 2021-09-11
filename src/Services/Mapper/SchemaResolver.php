<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

use function sprintf;
use function ucfirst;
use function var_dump;

class SchemaResolver implements RestApiBundle\Services\Mapper\SchemaResolverInterface
{
    public function resolve(string $class, bool $isNullable = false): RestApiBundle\Model\Mapper\Schema
    {
        $properties = [];
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

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

            if (!$isExposed) {
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
                throw new RestApiBundle\Exception\ContextAware\PropertyOfClassException($exception->getMessage(), class: $class, propertyName: $reflectionProperty->getName(), previous: $exception);
            }

            $properties[$reflectionProperty->getName()] = $propertySchema;
        }

        return RestApiBundle\Model\Mapper\Schema::createModelType($class, $properties, $isNullable);
    }

    private function resolveSchemaByMapping(RestApiBundle\Mapping\Mapper\TypeInterface $mapping): RestApiBundle\Model\Mapper\Schema
    {
        if ($mapping instanceof RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface) {
            $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(
                $mapping->getTransformerClass(),
                $mapping->getTransformerOptions(),
                $mapping->getIsNullable() ?? false
            );
        } elseif ($mapping instanceof RestApiBundle\Mapping\Mapper\ModelType) {
            $schema = $this->resolve($mapping->getClass(), $mapping->getIsNullable() ?? false);
        } elseif ($mapping instanceof RestApiBundle\Mapping\Mapper\ArrayType) {
            $valuesType = $mapping->getValuesType();
            if ($valuesType instanceof RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface && $valuesType->getTransformerClass() === RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class) {
                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(
                    RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::class,
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

    private function preprocessMapping(\ReflectionProperty $reflectionProperty, array $typeOptions = []): RestApiBundle\Mapping\Mapper\TypeInterface
    {
        $type = RestApiBundle\Helper\TypeExtractor::extractPropertyType($reflectionProperty);

        if (!$type) {
            throw new RestApiBundle\Exception\Mapper\Schema\InvalidDefinitionException('Expose are not allowed with empty property type.');
        }

        return $this->resolveMappingByType($type, $typeOptions);
    }

    private function resolveMappingByType(PropertyInfo\Type $type, array $typeOptions = []): RestApiBundle\Mapping\Mapper\TypeInterface
    {
        switch (true) {
            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING:
                $result = new RestApiBundle\Mapping\Mapper\StringType(nullable: $type->isNullable());

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_INT:
                $result = new RestApiBundle\Mapping\Mapper\IntegerType(nullable: $type->isNullable());

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_FLOAT:
                $result = new RestApiBundle\Mapping\Mapper\FloatType(nullable: $type->isNullable());

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_BOOL:
                $result = new RestApiBundle\Mapping\Mapper\BooleanType(nullable: $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isDateTime($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\DateTimeType(nullable: $type->isNullable());

                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                        $result->format = $typeOption->getFormat();
                    }
                }

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isDate($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\DateType(nullable: $type->isNullable());

                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                        $result->format = $typeOption->getFormat();
                    }
                }

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isTimestamp($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\TimestampType(nullable: $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\ModelType(class: (string) $type->getClassName(), nullable: $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\EntityType(class: (string) $type->getClassName(), nullable: $type->isNullable());

                foreach ($typeOptions as $typeOption) {
                    if ($typeOption instanceof RestApiBundle\Mapping\Mapper\FindByField) {
                        $result->field = $typeOption->getField();
                    }
                }

                break;

            case $type->isCollection():
                $result = new RestApiBundle\Mapping\Mapper\ArrayType($this->resolveMappingByType($type->getCollectionValueType(), $typeOptions), nullable: $type->isNullable());

                break;

            default:
                var_dump($type, RestApiBundle\Helper\ClassInstanceHelper::isDate($type->getClassName()));
                throw new \LogicException();
        }

        return $result;
    }
}
