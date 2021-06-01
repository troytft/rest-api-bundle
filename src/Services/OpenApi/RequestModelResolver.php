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

    public function __construct(
        RestApiBundle\Services\SettingsProvider $settingsProvider
    ) {
        $this->annotationReader = RestApiBundle\Helper\AnnotationReaderFactory::create(true);
        $this->settingsProvider = $settingsProvider;
    }

    public function resolveByClass(string $class, bool $nullable = false): OpenApi\Schema
    {
        if (!RestApiBundle\Helper\ClassInstanceHelper::isRequestModel($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s is not a request model.', $class));
        }

        $properties = [];
        $reflectedClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        foreach ($reflectedClass->getProperties() as $property) {
            $mappingAnnotation = null;
            $propertyConstraints = [];

            $annotations = $this->annotationReader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof RestApiBundle\Mapping\Mapper\TypeInterface) {
                    $mappingAnnotation = $annotation;
                }

                if ($annotation instanceof Validator\Constraint) {
                    $propertyConstraints[] = $annotation;
                }
            }

            if (!$mappingAnnotation) {
                continue;
            }

            $properties[$property->getName()] = $this->convert($mappingAnnotation, $propertyConstraints);
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
    private function convert(RestApiBundle\Mapping\Mapper\TypeInterface $type, array $validationConstraints): OpenApi\Schema
    {
        switch (true) {
            case $type instanceof RestApiBundle\Mapping\Mapper\ObjectTypeInterface:
                $result = $this->resolveByClass($type->getClassName(), $type->getNullable() === true);

                break;
            case $type->getTransformerClass() !== null:
                $result = $this->convertByTransformer($type, $validationConstraints);

                break;
            case $type instanceof RestApiBundle\Mapping\Mapper\CollectionTypeInterface:
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
    private function convertByTransformer(RestApiBundle\Mapping\Mapper\TypeInterface $type, array $validationConstraints): OpenApi\Schema
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
                if (!$type instanceof RestApiBundle\Mapping\Mapper\DateTimeType) {
                    throw new \LogicException();
                }

                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date-time',
                    'example' => RestApiBundle\Helper\OpenApi\ExampleHelper::getExampleDate()->format($type->format ?: $this->settingsProvider->getDefaultRequestDatetimeFormat()),
                    'nullable' => (bool) $type->getNullable(),
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\DateTransformer::class:
                if (!$type instanceof RestApiBundle\Mapping\Mapper\DateType) {
                    throw new \LogicException();
                }

                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date',
                    'example' => RestApiBundle\Helper\OpenApi\ExampleHelper::getExampleDate()->format($type->format ?: $this->settingsProvider->getDefaultRequestDateFormat()),
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
    private function convertCollectionType(RestApiBundle\Mapping\Mapper\CollectionTypeInterface $collectionType, array $validationConstraints): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $this->convert($collectionType->getValueType(), $validationConstraints),
            'nullable' => (bool) $collectionType->getNullable(),
        ]);
    }
}
