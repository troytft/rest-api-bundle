<?php

declare(strict_types=1);

namespace RestApiBundle\Services\OpenApi;

use cebe\openapi\spec as OpenApi;
use RestApiBundle;
use Symfony\Component\PropertyInfo;
use Symfony\Component\Validator;

class RequestModelResolver
{
    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        private RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver,
        private RestApiBundle\Services\PropertyInfoExtractorService $propertyInfoExtractorService,
    ) {
    }

    public function resolve(string $class, bool $nullable = false): OpenApi\Schema
    {
        if (!RestApiBundle\Helper\ReflectionHelper::isMapperModel($class)) {
            throw new \InvalidArgumentException(\sprintf('Class %s is not a request model', $class));
        }

        $properties = [];
        $reflectedClass = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class);

        $schema = $this->schemaResolver->resolve($class);

        foreach ($schema->properties as $propertyName => $propertyMapperSchema) {
            $reflectionProperty = $reflectedClass->getProperty($propertyName);
            $propertyConstraints = [];

            $annotations = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotations($reflectionProperty);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Validator\Constraint) {
                    $propertyConstraints[] = $annotation;
                }
            }

            $propertyOpenApiSchema = $this->resolveByMapperSchema($propertyMapperSchema, $propertyConstraints);

            if (RestApiBundle\Helper\ReflectionHelper::isDeprecated($reflectionProperty)) {
                $propertyOpenApiSchema->deprecated = true;
            }

            $properties[$propertyName] = $propertyOpenApiSchema;
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'properties' => $properties,
            'nullable' => $nullable,
        ]);
    }

    /**
     * @todo: refactor to more clear solution
     *
     * @param Validator\Constraint[] $constraints
     */
    private function applyConstraints(OpenApi\Schema $schema, array $constraints): void
    {
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
            RestApiBundle\Model\Mapper\Schema::UPLOADED_FILE_TYPE => $this->resolveUploadedFile($schema->isNullable),
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
     * @param Validator\Constraint[] $constraints
     */
    private function resolveTransformerAwareType(RestApiBundle\Model\Mapper\Schema $schema, array $constraints): OpenApi\Schema
    {
        $result = match ($schema->transformerClass) {
            RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class => RestApiBundle\Helper\OpenApi\SchemaHelper::createInteger($schema->isNullable),
            RestApiBundle\Services\Mapper\Transformer\StringTransformer::class => RestApiBundle\Helper\OpenApi\SchemaHelper::createString($schema->isNullable),
            RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class => RestApiBundle\Helper\OpenApi\SchemaHelper::createBoolean($schema->isNullable),
            RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class => RestApiBundle\Helper\OpenApi\SchemaHelper::createFloat($schema->isNullable),
            RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class => $this->resolveDateTimeTransformer($schema->transformerOptions, $schema->isNullable, $constraints),
            RestApiBundle\Services\Mapper\Transformer\DateTransformer::class => $this->resolveDateTransformer($schema->transformerOptions, $schema->isNullable),
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class => $this->resolveDoctrineEntityTransformer($schema->transformerOptions, $schema->isNullable),
            RestApiBundle\Services\Mapper\Transformer\EnumTransformer::class => $this->resolveEnumTransformer($schema->transformerOptions, $schema->isNullable),
            default => throw new \InvalidArgumentException(),
        };

        foreach ($constraints as $constraint) {
            if ($constraint instanceof Validator\Constraints\Range) {
                if ($constraint->min !== null) {
                    $result->minimum = $constraint->min;
                }

                if ($constraint->max !== null) {
                    $result->maximum = $constraint->max;
                }
            } elseif ($constraint instanceof Validator\Constraints\Choice) {
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

                $result->enum = $choices;
            } elseif ($constraint instanceof Validator\Constraints\Length) {
                if ($constraint->min !== null) {
                    $result->minLength = $constraint->min;
                }

                if ($constraint->max !== null) {
                    $result->maxLength = $constraint->max;
                }
            } elseif ($constraint instanceof Validator\Constraints\NotBlank) {
                if (!$constraint->allowNull) {
                    $result->nullable = false;
                }
            } elseif ($constraint instanceof Validator\Constraints\NotNull) {
                $result->nullable = false;
            }
        }

        return $result;
    }

    /**
     * @param Validator\Constraint[] $validationConstraints
     */
    private function resolveDateTimeTransformer(array $options, bool $nullable, array $validationConstraints): OpenApi\Schema
    {
        $format = $options[RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateTimeFormat();

        return RestApiBundle\Helper\OpenApi\SchemaHelper::createDateTime($format, $nullable);
    }

    private function resolveDateTransformer(array $options, bool $nullable): OpenApi\Schema
    {
        $format = $options[RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateFormat();

        return RestApiBundle\Helper\OpenApi\SchemaHelper::createDate($format, $nullable);
    }

    private function resolveDoctrineEntityTransformer(array $options, bool $nullable): OpenApi\Schema
    {
        $class = $options[RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::CLASS_OPTION];
        $fieldName = $options[RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::FIELD_OPTION];
        $isMultiple = $options[RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::MULTIPLE_OPTION] ?? false;

        $propertyType = $this->propertyInfoExtractorService->getRequiredPropertyType($class, $fieldName);
        if ($propertyType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_INT) {
            $schema = RestApiBundle\Helper\OpenApi\SchemaHelper::createInteger($nullable);
        } elseif ($propertyType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING) {
            $schema = RestApiBundle\Helper\OpenApi\SchemaHelper::createString($nullable);
        } else {
            throw new RestApiBundle\Exception\ContextAware\UnknownPropertyTypeException($class, $fieldName);
        }

        if ($isMultiple) {
            $schema = new OpenApi\Schema([
                'type' => OpenApi\Type::ARRAY,
                'items' => $schema,
                'nullable' => $nullable,
                'description' => \sprintf('Collection of "%s" fetched by field "%s"', $this->resolveShortClassName($class), $fieldName),
            ]);
        } else {
            $schema->description = \sprintf('"%s" fetched by field "%s"', $this->resolveShortClassName($class), $fieldName);
        }

        return $schema;
    }

    private function resolveEnumTransformer(array $options, bool $nullable): OpenApi\Schema
    {
        $class = $options[RestApiBundle\Services\Mapper\Transformer\EnumTransformer::CLASS_OPTION];

        return RestApiBundle\Helper\OpenApi\SchemaHelper::createEnum($class, $nullable);
    }

    private function resolveUploadedFile(bool $nullable): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => $nullable,
            'format' => 'binary',
        ]);
    }

    private function resolveShortClassName(string $class): string
    {
        $chunks = explode('\\', $class);

        return $chunks[array_key_last($chunks)] ?? throw new \LogicException();
    }
}
