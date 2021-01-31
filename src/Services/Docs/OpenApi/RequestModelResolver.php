<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator as Validator;
use RestApiBundle;
use Mapper;
use cebe\openapi\spec as OpenApi;
use function sprintf;

class RequestModelResolver
{
    /**
     * @var Mapper\SchemaGenerator
     */
    private $schemaGenerator;

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var RestApiBundle\Services\Docs\Types\TypeHintTypeReader
     */
    private $typeHintReader;

    /**
     * @var RestApiBundle\Services\Docs\Types\DocBlockTypeReader
     */
    private $docBlockReader;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(
        RestApiBundle\Services\Request\MapperInitiator $mapperInitiator,
        RestApiBundle\Services\Docs\OpenApi\DoctrineHelper $doctrineHelper,
        RestApiBundle\Services\Docs\Types\TypeHintTypeReader $typeHintReader,
        RestApiBundle\Services\Docs\Types\DocBlockTypeReader $docBlockReader
    ) {
        $this->schemaGenerator = $mapperInitiator->getMapper()->getSchemaGenerator();
        $this->doctrineHelper = $doctrineHelper;
        $this->typeHintReader = $typeHintReader;
        $this->docBlockReader = $docBlockReader;
        $this->annotationReader = Mapper\Helper\AnnotationReaderFactory::create(true);
    }

    public function resolveByClass(string $class, bool $nullable = false): OpenApi\Schema
    {
        if (!RestApiBundle\Helper\ClassInterfaceChecker::isRequestModel($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s is not a request model.', $class));
        }

        $properties = [];
        $reflectedClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        foreach ($reflectedClass->getProperties() as $property) {
            $propertySchema = null;
            $propertyConstraints = [];

            $annotations = $this->annotationReader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Mapper\DTO\Mapping\TypeInterface) {
                    $propertySchema = $this->convert($annotation);
                }

                if ($annotation instanceof Validator\Constraint) {
                    $propertyConstraints[] = $annotation;
                }
            }

            if (!$propertySchema) {
                continue;
            }

            foreach ($propertyConstraints as $constraint) {
                $this->applyConstraint($propertySchema, $constraint);
            }

            $properties[$property->getName()] = $propertySchema;
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'nullable' => $nullable,
            'properties' => $properties,
        ]);
    }

    private function applyConstraint(OpenApi\Schema $schema, Validator\Constraint $constraint): void
    {
        switch (true) {
            case $constraint instanceof Validator\Constraints\Range:
                if ($constraint->min !== null) {
                    $schema->minimum = $constraint->min;
                }

                if ($constraint->max !== null) {
                    $schema->maximum = $constraint->max;
                }

                break;

            case $constraint instanceof Validator\Constraints\Choice:
                if ($constraint->choices) {
                    $choices = $constraint->choices;
                } elseif ($constraint->callback) {
                    $callback = $constraint->callback;
                    $choices = $callback();
                } else {
                    throw new \InvalidArgumentException();
                }

                $schema->enum = $choices;

                break;

            case $constraint instanceof Validator\Constraints\Count:
                if ($constraint->min !== null) {
                    $schema->minItems = $constraint->min;
                }

                if ($constraint->max !== null) {
                    $schema->maxItems = $constraint->max;
                }

                break;

            case $constraint instanceof Validator\Constraints\Length:
                if ($constraint->min !== null) {
                    $schema->minLength = $constraint->min;
                }

                if ($constraint->max !== null) {
                    $schema->maxLength = $constraint->max;
                }

                break;
        }
    }

    private function convert(Mapper\DTO\Mapping\TypeInterface $type): OpenApi\Schema
    {
        if ($type instanceof Mapper\DTO\Mapping\ObjectTypeInterface) {
            $result = $this->resolveByClass($type->getClassName(), $type->getNullable() === true);
        } elseif ($type instanceof Mapper\DTO\Mapping\ScalarTypeInterface) {
            $result = $this->convertScalar($type);
        } elseif ($type instanceof Mapper\DTO\Mapping\CollectionTypeInterface) {
            $result = $this->convertCollectionType($type);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertScalar(Mapper\DTO\Mapping\ScalarTypeInterface $scalarType): OpenApi\Schema
    {
        switch ($scalarType->getTransformerName()) {
            case Mapper\Transformer\BooleanTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => $scalarType->getNullable() === true,
                ]);

                break;

            case Mapper\Transformer\IntegerTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $scalarType->getNullable() === true,
                ]);

                break;

            case Mapper\Transformer\StringTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $scalarType->getNullable() === true,
                ]);

                break;

            case Mapper\Transformer\FloatTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => $scalarType->getNullable() === true,
                ]);

                break;

            case Mapper\Transformer\DateTimeTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date-time',
                    'nullable' => $scalarType->getNullable() === true,
                ]);

                break;

            case Mapper\Transformer\DateTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date',
                    'nullable' => $scalarType->getNullable() === true,
                ]);

                break;

            case RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::getName():
                $className = $scalarType->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::CLASS_OPTION];
                $fieldName = $scalarType->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::FIELD_OPTION];

                $result = $this->doctrineHelper->getEntityFieldSchema($className, $fieldName, $scalarType->getNullable() === true);

                break;

            case RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::getName():
                $className = $scalarType->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::CLASS_OPTION];
                $fieldName = $scalarType->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::FIELD_OPTION];

                $innerSchema = $this->doctrineHelper->getEntityFieldSchema($className, $fieldName, false);
                $result = new RestApiBundle\DTO\Docs\Types\ArrayType($innerSchema, $scalarType->getNullable() === true);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Invalid type "%s"', $scalarType->getTransformerName()));
        }

        return $result;
    }

    private function convertCollectionType(Mapper\DTO\Mapping\CollectionTypeInterface $collectionType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $collectionType->getNullable() === true,
            'items' => $this->convert($collectionType->getType()),
        ]);
    }
}
