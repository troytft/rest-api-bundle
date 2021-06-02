<?php

namespace RestApiBundle\Services\OpenApi;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator as Validator;
use RestApiBundle;
use Symfony\Component\PropertyInfo;
use cebe\openapi\spec as OpenApi;

use function sprintf;

class RequestModelResolver extends RestApiBundle\Services\OpenApi\AbstractSchemaResolver
{
    private AnnotationReader $annotationReader;
    private RestApiBundle\Services\SettingsProvider $settingsProvider;
    private RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver;

    public function __construct(
        RestApiBundle\Services\SettingsProvider $settingsProvider,
        RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver
    ) {
        $this->annotationReader = RestApiBundle\Helper\AnnotationReaderFactory::create(true);
        $this->settingsProvider = $settingsProvider;
        $this->schemaResolver = $schemaResolver;
    }

    public function resolveByClass(string $class, bool $nullable = false): OpenApi\Schema
    {
        if (!RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s is not a request model.', $class));
        }

        $properties = [];
        $reflectedClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        $schema = $this->schemaResolver->resolveByClass($class);

        foreach ($schema->getProperties() as $propertyName => $propertySchema) {
            $property = $reflectedClass->getProperty($propertyName);
            $propertyConstraints = [];

            $annotations = $this->annotationReader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Validator\Constraint) {
                    $propertyConstraints[] = $annotation;
                }
            }

            $properties[$propertyName] = $this->convert($propertySchema, $propertyConstraints);
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'properties' => $properties,
            'nullable' => $nullable,
        ]);
    }

    private function applyConstraints(OpenApi\Schema $schema, array $constraints): void
    {
        foreach ($constraints as $constraint) {
            $this->applyConstraint($schema, $constraint);
        }
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

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function convert(RestApiBundle\Model\Mapper\Schema\TypeInterface $type, array $validationConstraints): OpenApi\Schema
    {
        switch (true) {
            case $type instanceof RestApiBundle\Model\Mapper\Schema\ObjectType:
                $result = $this->resolveByClass($type->getClass(), $type->getNullable());

                break;
            case $type->getTransformerClass() !== null:
                $result = $this->convertByTransformer($type, $validationConstraints);

                break;
            case $type instanceof RestApiBundle\Model\Mapper\Schema\CollectionType:
                $result = $this->convertCollectionType($type, $validationConstraints);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function convertByTransformer(RestApiBundle\Model\Mapper\Schema\TypeInterface $type, array $validationConstraints): OpenApi\Schema
    {
        switch ($type->getTransformerClass()) {
            case RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => (bool) $type->getNullable(),
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => (bool) $type->getNullable(),
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\StringTransformer::class:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => (bool) $type->getNullable(),
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => (bool) $type->getNullable(),
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class:
                $format = $type->getTransformerOptions()[RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateFormat();

                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date-time',
                    'example' => RestApiBundle\Helper\OpenApi\ExampleHelper::getExampleDate()->format($format),
                    'nullable' => (bool) $type->getNullable(),
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\DateTransformer::class:
                $format = $type->getTransformerOptions()[RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateFormat();
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date',
                    'example' => RestApiBundle\Helper\OpenApi\ExampleHelper::getExampleDate()->format($format),
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            case RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class:
                $class = $type->getTransformerOptions()[RestApiBundle\Services\Mapper\Transformer\EntityTransformer::CLASS_OPTION];
                $fieldName = $type->getTransformerOptions()[RestApiBundle\Services\Mapper\Transformer\EntityTransformer::FIELD_OPTION];

                $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($class, $fieldName);
                if ($columnType === PropertyInfo\Type::BUILTIN_TYPE_STRING) {
                    $result = new OpenApi\Schema([
                        'type' => OpenApi\Type::STRING,
                    ]);
                } elseif ($columnType === PropertyInfo\Type::BUILTIN_TYPE_INT) {
                    $result = new OpenApi\Schema([
                        'type' => OpenApi\Type::INTEGER,
                    ]);
                } else {
                    throw new \InvalidArgumentException();
                }

                $result->description = sprintf('Element by "%s"', $fieldName);
                $result->nullable = (bool) $type->getNullable();

                break;

            case RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::class:
                $class = $type->getTransformerOptions()[RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::CLASS_OPTION];
                $fieldName = $type->getTransformerOptions()[RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::FIELD_OPTION];

                $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($class, $fieldName);
                if ($columnType === PropertyInfo\Type::BUILTIN_TYPE_STRING) {
                    $arrayValueType = new OpenApi\Schema([
                        'type' => OpenApi\Type::STRING,
                        'nullable' => false,
                    ]);
                } elseif ($columnType === PropertyInfo\Type::BUILTIN_TYPE_INT) {
                    $arrayValueType = new OpenApi\Schema([
                        'type' => OpenApi\Type::INTEGER,
                        'nullable' => false,
                    ]);
                } else {
                    throw new \InvalidArgumentException();
                }

                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::ARRAY,
                    'items' => $arrayValueType,
                    'description' => sprintf('Array of elements by "%s"', $fieldName),
                    'nullable' => (bool) $type->getNullable(),
                ]);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Invalid type "%s"', $type->getTransformerClass()));
        }

        return $result;
    }

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function convertCollectionType(RestApiBundle\Model\Mapper\Schema\CollectionType $collectionType, array $validationConstraints): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $this->convert($collectionType->getValuesType(), $validationConstraints),
            'nullable' => (bool) $collectionType->getNullable(),
        ]);
    }
}
