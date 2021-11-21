<?php

namespace RestApiBundle\Services\OpenApi\Specification;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\PropertyInfo;
use Symfony\Component\Validator as Validator;

use function array_is_list;
use function sprintf;

class RequestModelResolver
{
    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        private RestApiBundle\Services\Mapper\SchemaResolver $mapperSchemaResolver,
    ) {
    }

    public function resolve(string $class, bool $nullable = false): OpenApi\Schema
    {
        if (!RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s is not a request model.', $class));
        }

        $properties = [];
        $reflectedClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        $schema = $this->mapperSchemaResolver->resolve($class);

        foreach ($schema->properties as $propertyName => $propertySchema) {
            $reflectionProperty = $reflectedClass->getProperty($propertyName);
            $propertyConstraints = [];

            $annotations = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotations($reflectionProperty);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Validator\Constraint) {
                    $propertyConstraints[] = $annotation;
                }
            }

            $properties[$propertyName] = $this->resolveByMapperSchema($propertySchema, $propertyConstraints);
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'properties' => $properties,
            'nullable' => $nullable,
        ]);
    }

    /**
     * @param Validator\Constraint[] $constraints
     */
    private function applyConstraints(OpenApi\Schema $schema, array $constraints): void
    {
        foreach ($constraints as $constraint) {
            $this->applyConstraint($schema, $constraint);
        }
    }

    /**
     * @todo: refactor to more clear solution
     */
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

                if (!array_is_list($choices)) {
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
    private function resolveByMapperSchema(RestApiBundle\Model\Mapper\Schema $schema, array $validationConstraints): OpenApi\Schema
    {
        return match ($schema->type) {
            RestApiBundle\Model\Mapper\Schema::MODEL_TYPE => $this->resolve($schema->class, $schema->isNullable),
            RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE => $this->resolveArrayType($schema, $validationConstraints),
            RestApiBundle\Model\Mapper\Schema::TRANSFORMER_AWARE_TYPE => $this->resolveTransformerAwareType($schema, $validationConstraints),
            default => throw new \InvalidArgumentException(),
        };
    }

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function resolveArrayType(RestApiBundle\Model\Mapper\Schema $schema, array $validationConstraints): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $this->resolveByMapperSchema($schema->valuesType, $validationConstraints),
            'nullable' => $schema->isNullable,
        ]);
    }

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function resolveTransformerAwareType(RestApiBundle\Model\Mapper\Schema $schema, array $validationConstraints): OpenApi\Schema
    {
        return match ($schema->transformerClass) {
            RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class => $this->resolveScalarTransformer(PropertyInfo\Type::BUILTIN_TYPE_INT, $schema->isNullable, $validationConstraints),
            RestApiBundle\Services\Mapper\Transformer\StringTransformer::class => $this->resolveScalarTransformer(PropertyInfo\Type::BUILTIN_TYPE_STRING, $schema->isNullable, $validationConstraints),
            RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class => $this->resolveScalarTransformer(PropertyInfo\Type::BUILTIN_TYPE_BOOL, $schema->isNullable, $validationConstraints),
            RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class => $this->resolveScalarTransformer(PropertyInfo\Type::BUILTIN_TYPE_FLOAT, $schema->isNullable, $validationConstraints),
            RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class => $this->resolveDateTimeTransformer($schema->transformerOptions, $schema->isNullable, $validationConstraints),
            RestApiBundle\Services\Mapper\Transformer\DateTransformer::class => $this->resolveDateTransformer($schema->transformerOptions, $schema->isNullable),
            RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class => $this->resolveEntityTransformer($schema->transformerOptions, $schema->isNullable),
            RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::class => $this->resolveEntitiesCollectionTransformer($schema->transformerOptions, $schema->isNullable),
            default => throw new \InvalidArgumentException(),
        };
    }

    private function resolveScalarTransformer(string $type, bool $nullable, array $validationConstraints): OpenApi\Schema
    {
        $result = RestApiBundle\Helper\OpenApiHelper::createScalarFromString($type, $nullable);
        $this->applyConstraints($result, $validationConstraints);

        return $result;
    }

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function resolveDateTimeTransformer(array $options, bool $nullable, array $validationConstraints): OpenApi\Schema
    {
        $format = $options[RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateTimeFormat();
        $result = RestApiBundle\Helper\OpenApiHelper::createDateTime($format, $nullable);

        $this->applyConstraints($result, $validationConstraints);

        return $result;
    }

    private function resolveDateTransformer(array $options, bool $nullable): OpenApi\Schema
    {
        $format = $options[RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateFormat();

        return RestApiBundle\Helper\OpenApiHelper::createDate($format, $nullable);
    }

    private function resolveEntityTransformer(array $options, bool $nullable): OpenApi\Schema
    {
        $class = $options[RestApiBundle\Services\Mapper\Transformer\EntityTransformer::CLASS_OPTION];
        $fieldName = $options[RestApiBundle\Services\Mapper\Transformer\EntityTransformer::FIELD_OPTION];
        $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($class, $fieldName);

        $result = RestApiBundle\Helper\OpenApiHelper::createScalarFromString($columnType);
        $result->description = sprintf('Element by "%s"', $fieldName);
        $result->nullable = $nullable;

        return $result;
    }

    private function resolveEntitiesCollectionTransformer(array $options, bool $nullable): OpenApi\Schema
    {
        $class = $options[RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::CLASS_OPTION];
        $fieldName = $options[RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::FIELD_OPTION];
        $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($class, $fieldName);
        $itemsType = RestApiBundle\Helper\OpenApiHelper::createScalarFromString($columnType);

        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $itemsType,
            'nullable' => $nullable,
            'description' => sprintf('Array of elements by "%s"', $fieldName),
        ]);
    }
}
