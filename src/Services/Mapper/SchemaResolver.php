<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\PropertyInfo;
use Doctrine\Common\Annotations\AnnotationReader;

use function ucfirst;

class SchemaResolver implements RestApiBundle\Services\Mapper\SchemaResolverInterface
{
    private AnnotationReader $annotationReader;

    public function __construct()
    {
        $this->annotationReader = RestApiBundle\Helper\AnnotationReaderFactory::create(true);
    }

    public function resolve(string $class, bool $isNullable = false): RestApiBundle\Model\Mapper\Schema
    {
        $properties = [];
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $mapping = $this->annotationReader->getPropertyAnnotation($reflectionProperty, RestApiBundle\Mapping\Mapper\TypeInterface::class);
            if (!$mapping instanceof RestApiBundle\Mapping\Mapper\TypeInterface) {
                continue;
            }

            if ($mapping instanceof RestApiBundle\Mapping\Mapper\AutoType) {
                $mapping = $this->resolveMappingByReflection($reflectionProperty);
            }

            $propertySchema = $this->resolveSchemaByMapping($mapping);
            $propertySetterName = 'set' . ucfirst($reflectionProperty->getName());

            if ($reflectionClass->hasMethod($propertySetterName) && $reflectionClass->getMethod($propertySetterName)->isPublic()) {
                $propertySchema->setPropertySetterName($propertySetterName);
            } elseif (!$reflectionProperty->isPublic()) {
                throw new RestApiBundle\Exception\Mapper\SetterDoesNotExistException($propertySetterName);
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
                $mapping->getIsNullable()
            );
        } elseif ($mapping instanceof RestApiBundle\Mapping\Mapper\ModelType) {
            $schema = $this->resolve($mapping->class, $mapping->nullable);
        } elseif ($mapping instanceof RestApiBundle\Mapping\Mapper\ArrayType) {
            $valuesType = $mapping->getValuesType();
            if ($valuesType instanceof RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface && $valuesType->getTransformerClass() === RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class) {
                $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(
                    RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::class,
                    $valuesType->getTransformerOptions(),
                    $valuesType->getIsNullable()
                );
            } else {
                $schema = RestApiBundle\Model\Mapper\Schema::createArrayType($this->resolveSchemaByMapping($valuesType), $mapping->nullable);
            }
        } else {
            throw new \InvalidArgumentException();
        }

        return $schema;
    }

    private function resolveMappingByReflection(\ReflectionProperty $reflectionProperty): RestApiBundle\Mapping\Mapper\TypeInterface
    {
        if (!$reflectionProperty->getType()) {
            throw new \LogicException();
        }

        $type = RestApiBundle\Helper\TypeExtractor::extractByReflectionType($reflectionProperty->getType());
        switch (true) {
            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING:
                $result = new RestApiBundle\Mapping\Mapper\StringType();
                $result
                    ->nullable = $type->isNullable();

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_INT:
                $result = new RestApiBundle\Mapping\Mapper\IntegerType();
                $result
                    ->nullable = $type->isNullable();

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_FLOAT:
                $result = new RestApiBundle\Mapping\Mapper\FloatType();
                $result
                    ->nullable = $type->isNullable();

                break;

            case $type->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_BOOL:
                $result = new RestApiBundle\Mapping\Mapper\BooleanType();
                $result
                    ->nullable = $type->isNullable();

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isDateTime($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\DateTimeType();
                $result
                    ->nullable = $type->isNullable();

                break;

            case $type->getClassName() && RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\ModelType();
                $result->class = (string) $type->getClassName();
                $result->nullable = $type->isNullable();

                break;

            case $type->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($type->getClassName()):
                $result = new RestApiBundle\Mapping\Mapper\EntityType();
                $result->class = (string) $type->getClassName();
                $result->nullable = $type->isNullable();

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }
}
