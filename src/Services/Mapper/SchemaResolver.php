<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Doctrine\Common\Annotations\AnnotationReader;

use function get_class;
use function ucfirst;

class SchemaResolver
{
    private AnnotationReader $annotationReader;

    public function __construct()
    {
        $this->annotationReader = RestApiBundle\Helper\AnnotationReaderFactory::create(true);
    }

    public function resolveByInstance(RestApiBundle\Mapping\Mapper\ModelInterface $model): RestApiBundle\Model\Mapper\Schema\ObjectType
    {
        return $this->processObjectType(null, [], true, get_class($model));
    }

    public function resolveByClass(string $class): RestApiBundle\Model\Mapper\Schema\ObjectType
    {
        return $this->processObjectType(null, [], true, $class);
    }

    private function processObjectType(?string $transformerName, array $transformerOptions, bool $isNullable, string $className): RestApiBundle\Model\Mapper\Schema\ObjectType
    {
        $className = ltrim($className, '\\');
        
        $properties = [];
        $reflectionClass = new \ReflectionClass($className);

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
            ->setClassName($className)
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
                    $mapping->getNullable() ?: true,
                    $mapping->getClassName()
                );

                break;

            case $mapping instanceof RestApiBundle\Mapping\Mapper\ScalarTypeInterface:
                $schema = $this->processScalarType(
                    $mapping->getTransformerClass(),
                    $mapping->getTransformerOptions(),
                    $mapping->getNullable() ?: true
                );

                break;

            case $mapping instanceof RestApiBundle\Mapping\Mapper\CollectionTypeInterface:
                $schema = $this->processCollectionType(
                    $mapping->getTransformerClass(),
                    $mapping->getTransformerOptions(),
                    $mapping->getNullable() ?: true,
                    $mapping->getValueType()
                );

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $schema;
    }
}
