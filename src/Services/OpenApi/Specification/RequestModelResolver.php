<?php

namespace RestApiBundle\Services\OpenApi\Specification;

use Symfony\Component\Validator as Validator;
use RestApiBundle;
use Symfony\Component\PropertyInfo;
use cebe\openapi\spec as OpenApi;

use function array_is_list;
use function sprintf;

class RequestModelResolver
{
    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        private RestApiBundle\Services\Mapper\SchemaResolver $mapperSchemaResolver,
    ) {
    }

    public function resolveAsRequestBody(string $class): OpenApi\RequestBody
    {
        return new OpenApi\RequestBody([
            'description' => 'Request body',
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => $this->toSchema($class),
                ]
            ]
        ]);
    }

    /**
     * @return OpenApi\Parameter[]
     */
    public function resolveAsQueryParameters(string $class): array
    {
        $queryParameters = [];
        $requestModelSchema = $this->toSchema($class);

        foreach ($requestModelSchema->properties as $propertyName => $propertySchema) {
            $parameter = new OpenApi\Parameter([
                'in' => 'query',
                'name' => $propertyName,
                'required' => !$propertySchema->nullable,
                'schema' => $propertySchema,
            ]);

            // Swagger UI shows description only from parameters
            if ($propertySchema->description) {
                $parameter->description = $propertySchema->description;
                unset($propertySchema->description);
            }

            $queryParameters[] = $parameter;
        }

        return $queryParameters;
    }

    /**
     * @todo: migrate tests and make private
     */
    public function toSchema(string $class, bool $nullable = false): OpenApi\Schema
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
    private function convert(RestApiBundle\Model\Mapper\Schema $schema, array $validationConstraints): OpenApi\Schema
    {
        switch ($schema->type) {
            case RestApiBundle\Model\Mapper\Schema::MODEL_TYPE:
                $result = $this->toSchema($schema->class, $schema->isNullable);

                break;
            case RestApiBundle\Model\Mapper\Schema::TRANSFORMER_AWARE_TYPE:
                $result = $this->convertScalarType($schema, $validationConstraints);

                break;
            case RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE:
                $result = $this->convertCollectionType($schema, $validationConstraints);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function convertScalarType(RestApiBundle\Model\Mapper\Schema $schema, array $validationConstraints): OpenApi\Schema
    {
        switch ($schema->transformerClass) {
            case RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => $schema->isNullable,
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $schema->isNullable,
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\StringTransformer::class:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $schema->isNullable,
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => $schema->isNullable,
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class:
                $format = $schema->transformerOptions[RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateTimeFormat();

                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date-time',
                    'example' => RestApiBundle\Helper\OpenApi\ExampleHelper::getExampleDate()->format($format),
                    'nullable' => $schema->isNullable,
                ]);
                $this->applyConstraints($result, $validationConstraints);

                break;

            case RestApiBundle\Services\Mapper\Transformer\DateTransformer::class:
                $format = $schema->transformerOptions[RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateFormat();
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'format' => 'date',
                    'example' => RestApiBundle\Helper\OpenApi\ExampleHelper::getExampleDate()->format($format),
                    'nullable' => $schema->isNullable,
                ]);

                break;

            case RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class:
                $class = $schema->transformerOptions[RestApiBundle\Services\Mapper\Transformer\EntityTransformer::CLASS_OPTION];
                $fieldName = $schema->transformerOptions[RestApiBundle\Services\Mapper\Transformer\EntityTransformer::FIELD_OPTION];

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
                $result->nullable = $schema->isNullable;

                break;

            case RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::class:
                $class = $schema->transformerOptions[RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::CLASS_OPTION];
                $fieldName = $schema->transformerOptions[RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::FIELD_OPTION];

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
                    'nullable' => $schema->isNullable,
                ]);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Invalid type "%s"', $schema->transformerClass));
        }

        return $result;
    }

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function convertCollectionType(RestApiBundle\Model\Mapper\Schema $schema, array $validationConstraints): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'items' => $this->convert($schema->valuesType, $validationConstraints),
            'nullable' => $schema->isNullable,
        ]);
    }
}
