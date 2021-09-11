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

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $mapping = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotation($reflectionProperty, RestApiBundle\Mapping\Mapper\TypeInterface::class);
            if (!$mapping instanceof RestApiBundle\Mapping\Mapper\TypeInterface) {
                continue;
            }

            try {
                $mapping = $this->preprocessMapping($reflectionProperty, $mapping);
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

    private function preprocessMapping(\ReflectionProperty $reflectionProperty, RestApiBundle\Mapping\Mapper\TypeInterface $originalMapping): RestApiBundle\Mapping\Mapper\TypeInterface
    {
        $type = RestApiBundle\Helper\TypeExtractor::extractPropertyType($reflectionProperty);

        if (!$type && $originalMapping instanceof RestApiBundle\Mapping\Mapper\Field) {
            throw new RestApiBundle\Exception\Mapper\Schema\InvalidDefinitionException('Field are not allowed with empty property type.');
        } elseif ($type && $originalMapping instanceof RestApiBundle\Mapping\Mapper\Field) {
            return $this->resolveMappingByType($type);
        } elseif (!$type) {
            return $originalMapping;
        }

        if ($originalMapping instanceof RestApiBundle\Mapping\Mapper\NullableAwareTypeInterface && $originalMapping->getIsNullable() === null) {
            $originalMapping->setIsNullable($type->isNullable());
        }

        if ($originalMapping instanceof RestApiBundle\Mapping\Mapper\EntityType && $originalMapping->getField() && !$originalMapping->getClass()) {
            if (!$type->getClassName()) {
                throw new \LogicException();
            }

            $originalMapping = new RestApiBundle\Mapping\Mapper\EntityType(class: $type->getClassName(), field: $originalMapping->getField(), nullable: $originalMapping->getIsNullable() ?? $type->isNullable());
        }
        
        return $originalMapping;
    }

    private function resolveMappingByType(PropertyInfo\Type $type): RestApiBundle\Mapping\Mapper\TypeInterface
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

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\ModelType(class: (string) $type->getClassName(), nullable: $type->isNullable());

                break;

            case $type->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\EntityType(class: (string) $type->getClassName(), nullable: $type->isNullable());

                break;

            case $type->isCollection():
                $result = new RestApiBundle\Mapping\Mapper\ArrayType($this->resolveMappingByType($type->getCollectionValueType()), nullable: $type->isNullable());

                break;

            default:
                throw new \LogicException();
        }

        return $result;
    }
}
