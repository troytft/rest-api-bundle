<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Doctrine\Common\Annotations\AnnotationReader;

use function get_class;
use function ltrim;
use function ucfirst;

class SchemaResolver
{
    private AnnotationReader $annotationReader;
    /** @var array<string, RestApiBundle\Model\Mapper\Schema\ObjectType> */
    private array $cache = [];

    public function __construct()
    {
        $this->annotationReader = RestApiBundle\Helper\AnnotationReaderFactory::create(true);
    }

    public function resolveByInstance(RestApiBundle\Mapping\Mapper\ModelInterface $model): RestApiBundle\Model\Mapper\Schema\ObjectType
    {
        return $this->resolveByClass(get_class($model));
    }

    public function resolveByClass(string $class): RestApiBundle\Model\Mapper\Schema\ObjectType
    {
        $class = ltrim($class, '\\');
        if (!isset($this->cache[$class])) {
            $this->cache[$class] = $this->processObjectType(null, [], false, $class);
        }

        return $this->cache[$class];
    }

    private function processObjectType(?string $transformerName, array $transformerOptions, bool $isNullable, string $class): RestApiBundle\Model\Mapper\Schema\ObjectType
    {
        $properties = [];
        $reflectionClass = new \ReflectionClass($class);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, RestApiBundle\Mapping\Mapper\TypeInterface::class);
            if (!$annotation instanceof RestApiBundle\Mapping\Mapper\TypeInterface) {
                continue;
            }

            $propertySchema = $this->processType($annotation);

            $setterName = 'set' . ucfirst($reflectionProperty->getName());
            if ($reflectionClass->hasMethod($setterName) && $reflectionClass->getMethod($setterName)->isPublic()) {
                $propertySchema->setSetterName($setterName);
            } elseif (!$reflectionProperty->isPublic()) {
                throw new RestApiBundle\Exception\Mapper\SetterDoesNotExistException($setterName);
            }

            $properties[$reflectionProperty->getName()] = $propertySchema;
        }

        $schema = new RestApiBundle\Model\Mapper\Schema\ObjectType();
        $schema
            ->setClass($class)
            ->setNullable($isNullable)
            ->setProperties($properties)
            ->setTransformerName($transformerName)
            ->setTransformerOptions($transformerOptions);
        
        return $schema;
    }

    private function processScalarType(?string $transformerName, array $transformerOptions, bool $isNullable): RestApiBundle\Model\Mapper\Schema\ScalarType
    {
        $schema = new RestApiBundle\Model\Mapper\Schema\ScalarType();
        $schema
            ->setNullable($isNullable)
            ->setTransformerName($transformerName)
            ->setTransformerOptions($transformerOptions);

        return $schema;
    }

    private function processCollectionType(?string $transformerName, array $transformerOptions, bool $isNullable, RestApiBundle\Mapping\Mapper\TypeInterface $valuesType): RestApiBundle\Model\Mapper\Schema\CollectionType
    {
        $schema = new RestApiBundle\Model\Mapper\Schema\CollectionType();
        $schema
            ->setValuesType($this->processType($valuesType))
            ->setNullable($isNullable)
            ->setTransformerName($transformerName)
            ->setTransformerOptions($transformerOptions);

        return $schema;
    }

    private function processType(RestApiBundle\Mapping\Mapper\TypeInterface $mapping): RestApiBundle\Model\Mapper\Schema\TypeInterface
    {
        switch (true) {
            case $mapping instanceof RestApiBundle\Mapping\Mapper\ObjectTypeInterface:
                $schema = $this->processObjectType(
                    $mapping->getTransformerClass(),
                    $mapping->getTransformerOptions(),
                    $mapping->getNullable() ?: false,
                    $mapping->getClassName()
                );

                break;

            case $mapping instanceof RestApiBundle\Mapping\Mapper\ScalarTypeInterface:
                $schema = $this->processScalarType(
                    $mapping->getTransformerClass(),
                    $mapping->getTransformerOptions(),
                    $mapping->getNullable() ?: false
                );

                break;

            case $mapping instanceof RestApiBundle\Mapping\Mapper\CollectionTypeInterface:
                $schema = $this->processCollectionType(
                    $mapping->getTransformerClass(),
                    $mapping->getTransformerOptions(),
                    $mapping->getNullable() ?: false,
                    $mapping->getValueType()
                );

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $schema;
    }
}
