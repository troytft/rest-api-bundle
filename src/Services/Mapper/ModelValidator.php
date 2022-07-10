<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function array_merge_recursive;
use function explode;
use function get_class;
use function implode;
use function is_numeric;
use function sprintf;
use function str_replace;
use function ucfirst;

class ModelValidator
{
    public function __construct(
        private RestApiBundle\Services\Mapper\SchemaResolverInterface $schemaResolver,
        private ValidatorInterface $validator
    ) {
    }


    /**
     * @return array<string, string[]>
     */
    public function validate(RestApiBundle\Mapping\Mapper\ModelInterface $model): array
    {
        return array_merge_recursive($this->getFirstLevelErrors($model), $this->getNestedErrors($model));
    }

    /**
     * @return array<string, string[]>
     */
    private function getFirstLevelErrors(RestApiBundle\Mapping\Mapper\ModelInterface $model): array
    {
        $errors = [];
        $violations = $this->validator->validate($model);

        foreach ($violations as $violation) {
            $path = $this->normalizeConstraintViolationPath($violation);
            if (!isset($errors[$path])) {
                $errors[$path] = [];
            }

            $errors[$path][] = $violation->getMessage();
        }

        return $errors;
    }

    /**
     * @return array<string, string[]>
     */
    private function getNestedErrors(RestApiBundle\Mapping\Mapper\ModelInterface $model): array
    {
        $result = [];

        $schema = $this->schemaResolver->resolve(get_class($model));

        /** @var RestApiBundle\Model\Mapper\Schema $propertySchema */
        foreach ($schema->properties as $propertyName => $propertySchema) {
            if ($propertySchema->type === RestApiBundle\Model\Mapper\Schema::MODEL_TYPE) {
                $propertyValue = $this->getPropertyValueFromInstance($model, $propertyName);
                if (!$propertyValue) {
                    continue;
                }

                $innerErrors = $this->validate($propertyValue);
                if ($innerErrors) {
                    $prefix = sprintf('%s.', $propertyName);
                    $result[] = $this->appendPrefixToArrayKeys($prefix, $innerErrors);
                }
            } elseif ($propertySchema->type === RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE && $propertySchema->valuesType->type === RestApiBundle\Model\Mapper\Schema::MODEL_TYPE) {
                $propertyValue = $this->getPropertyValueFromInstance($model, $propertyName);
                if (!$propertyValue) {
                    continue;
                }

                foreach ($propertyValue as $itemIndex => $itemValue) {
                    $innerErrors = $this->validate($itemValue);
                    if ($innerErrors) {
                        $prefix = sprintf('%s.%d.', $propertyName, $itemIndex);
                        $result[] = $this->appendPrefixToArrayKeys($prefix, $innerErrors);
                    }
                }
            }
        }

        if (!$result) {
            return [];
        }

        return array_merge(...$result);
    }

    private function getPropertyValueFromInstance(RestApiBundle\Mapping\Mapper\ModelInterface $instance, string $propertyName): mixed
    {
        $getterName = 'get' . ucfirst($propertyName);

        if (method_exists($instance, $getterName)) {
            return $instance->{$getterName}();
        }

        return $instance->$propertyName;
    }

    private function appendPrefixToArrayKeys(string $prefix, array $array)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[$prefix . $key] = $value;
        }

        return $result;
    }

    private function normalizeConstraintViolationPath(ConstraintViolationInterface $constraintViolation): string
    {
        $path = $constraintViolation->getPropertyPath();
        if (str_contains($path, '[')) {
            $path = str_replace(['[', ']'], ['.', ''], $path);
        }

        $pathParts = [];
        $schema = $this->schemaResolver->resolve(get_class($constraintViolation->getRoot()));
        foreach (explode('.', $path) as $part) {
            $property = $schema->properties[$part] ?? null;
            if ($property instanceof RestApiBundle\Model\Mapper\Schema) {
                $pathParts[] = $part;
                $schema = $property;
            } elseif (is_numeric($part)) {
                $pathParts[] = $part;
            } else {
                $pathParts[] = '*';

                break;
            }
        }

        return implode('.', $pathParts);
    }

    private function hasProperty(RestApiBundle\Mapping\Mapper\ModelInterface $model, string $propertyName): bool
    {
        $schema = $this->schemaResolver->resolve(get_class($model));

        return isset($schema->properties[$propertyName]);
    }
}
