<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\PropertyInfo;
use Doctrine\Common\Annotations\AnnotationReader;

use function ltrim;
use function ucfirst;

class SchemaResolver
{
    private AnnotationReader $annotationReader;
    /** @var array<string, RestApiBundle\Model\Mapper\Schema> */
    private array $cache = [];

    public function __construct()
    {
        $this->annotationReader = RestApiBundle\Helper\AnnotationReaderFactory::create(true);
    }

    public function resolve(string $class, bool $isNullable = false): RestApiBundle\Model\Mapper\Schema
    {
        $key = ltrim($class, '\\') . $isNullable;

        if (!isset($this->cache[$key])) {
            $properties = [];
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                $annotation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, RestApiBundle\Mapping\Mapper\TypeInterface::class);
                if (!$annotation instanceof RestApiBundle\Mapping\Mapper\TypeInterface) {
                    continue;
                }

                if ($annotation instanceof RestApiBundle\Mapping\Mapper\AutoType) {
                    $annotation = $this->resolveAutoType($reflectionProperty);
                }

                $propertySchema = $this->processType($annotation);
                $propertySetterName = 'set' . ucfirst($reflectionProperty->getName());

                if ($reflectionClass->hasMethod($propertySetterName) && $reflectionClass->getMethod($propertySetterName)->isPublic()) {
                    $propertySchema->setPropertySetterName($propertySetterName);
                } elseif (!$reflectionProperty->isPublic()) {
                    throw new RestApiBundle\Exception\Mapper\SetterDoesNotExistException($propertySetterName);
                }

                $properties[$reflectionProperty->getName()] = $propertySchema;
            }

            $this->cache[$key] = RestApiBundle\Model\Mapper\Schema::createModelType($class, $properties, $isNullable);
        }

        return $this->cache[$key];
    }

    private function processType(RestApiBundle\Mapping\Mapper\TypeInterface $mapping): RestApiBundle\Model\Mapper\Schema
    {
        $isNullable = $mapping->getIsNullable() ?: false;

        if ($mapping instanceof RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface) {
            $schema = RestApiBundle\Model\Mapper\Schema::createTransformerAwareType(
                $mapping->getTransformerClass(),
                $mapping->getTransformerOptions(),
                $mapping->getIsNullable()
            );
        } elseif ($mapping instanceof RestApiBundle\Mapping\Mapper\ModelType) {
            $schema = $this->resolve($mapping->class, $isNullable);
        } elseif ($mapping instanceof RestApiBundle\Mapping\Mapper\ArrayType) {
            $schema = RestApiBundle\Model\Mapper\Schema::createArrayType($this->processType($mapping->getValueType()), $isNullable);
        } else {
            throw new \InvalidArgumentException();
        }

        return $schema;
    }

    private function resolveAutoType(\ReflectionProperty $reflectionProperty): RestApiBundle\Mapping\Mapper\TypeInterface
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
