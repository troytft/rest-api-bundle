<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator as Validator;
use RestApiBundle;
use Mapper;
use cebe\openapi\spec as OpenApi;

use function sprintf;

class RequestModelResolver extends RestApiBundle\Services\Docs\OpenApi\AbstractSchemaResolver
{
    /**
     * @var Mapper\SchemaGenerator
     */
    private $schemaGenerator;

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\DoctrineResolver
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
        RestApiBundle\Services\Docs\OpenApi\DoctrineResolver $doctrineHelper,
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
            'properties' => $properties,
            'nullable' => $nullable,
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
        switch (true) {
            case $type instanceof Mapper\DTO\Mapping\ObjectTypeInterface:
                $result = $this->resolveByClass($type->getClassName(), $type->getNullable() === true);

                break;
            case $type->getTransformerName() !== null:
                $result = $this->convertByTransformer($type);

                break;
            case $type instanceof Mapper\DTO\Mapping\CollectionTypeInterface:
                $result = $this->convertCollectionType($type);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertByTransformer(Mapper\DTO\Mapping\TypeInterface $type): OpenApi\Schema
    {
        switch ($type->getTransformerName()) {
            case Mapper\Transformer\BooleanTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            case Mapper\Transformer\IntegerTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            case Mapper\Transformer\StringTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            case Mapper\Transformer\FloatTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            case Mapper\Transformer\DateTimeTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date-time',
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            case Mapper\Transformer\DateTransformer::getName():
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date',
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            case RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::getName():
                $class = $type->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::CLASS_OPTION];
                $fieldName = $type->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::FIELD_OPTION];

                $result = $this->doctrineHelper->resolveByColumnType($class, $fieldName);
                $result->description = sprintf('Element by "%s"', $fieldName);
                $result->nullable = (bool) $type->getNullable();

                break;

            case RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::getName():
                $class = $type->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::CLASS_OPTION];
                $fieldName = $type->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::FIELD_OPTION];
                $columnType = $this->doctrineHelper->resolveByColumnType($class, $fieldName);
                $columnType
                    ->nullable = false;

                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::ARRAY,
                    'items' => $columnType,
                    'description' => sprintf('Array of elements by "%s"', $fieldName),
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Invalid type "%s"', $type->getTransformerName()));
        }

        return $result;
    }

    private function convertCollectionType(Mapper\DTO\Mapping\CollectionTypeInterface $collectionType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $this->convert($collectionType->getType()),
            'nullable' => (bool) $collectionType->getNullable(),
        ]);
    }
}
