<?php

namespace RestApiBundle\Services\RequestModel;

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
use function strpos;
use function ucfirst;

class RequestModelValidator
{
    private RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver;
    private ValidatorInterface $validator;

    public function __construct(
        RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver,
        ValidatorInterface $validator
    ) {
        $this->schemaResolver = $schemaResolver;
        $this->validator = $validator;
    }

    /**
     * @return array<string, string[]>
     */
    public function validate(RestApiBundle\Mapping\RequestModel\RequestModelInterface $requestModel): array
    {
        return array_merge_recursive($this->getFirstLevelErrors($requestModel), $this->getNestedErrors($requestModel));
    }

    /**
     * @return array<string, string[]>
     */
    private function getFirstLevelErrors(RestApiBundle\Mapping\RequestModel\RequestModelInterface $requestModel): array
    {
        $errors = [];
        $violations = $this->validator->validate($requestModel);

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
    private function getNestedErrors(RestApiBundle\Mapping\RequestModel\RequestModelInterface $requestModel): array
    {
        $result = [];

        $schema = $this->schemaResolver->resolve(get_class($requestModel));

        foreach ($schema->getProperties() as $propertyName => $propertyType) {
            if ($propertyType instanceof RestApiBundle\Model\Mapper\Schema\ObjectType) {
                $propertyValue = $this->getPropertyValueFromInstance($requestModel, $propertyName);
                if (!$propertyValue) {
                    continue;
                }

                $innerErrors = $this->validate($propertyValue);
                if ($innerErrors) {
                    $prefix = sprintf('%s.', $propertyName);
                    $result[] = $this->appendPrefixToArrayKeys($prefix, $innerErrors);
                }
            } elseif ($propertyType instanceof RestApiBundle\Model\Mapper\Schema\CollectionType && $propertyType->getValuesType() instanceof RestApiBundle\Model\Mapper\Schema\ObjectType) {
                $propertyValue = $this->getPropertyValueFromInstance($requestModel, $propertyName);
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

    /**
     * @param RestApiBundle\Mapping\RequestModel\RequestModelInterface $instance
     * @param string $propertyName
     *
     * @return RestApiBundle\Mapping\RequestModel\RequestModelInterface|RestApiBundle\Mapping\RequestModel\RequestModelInterface[]:null
     */
    private function getPropertyValueFromInstance(RestApiBundle\Mapping\RequestModel\RequestModelInterface $instance, string $propertyName)
    {
        $getterName = 'get' . ucfirst($propertyName);

        return $instance->{$getterName}();
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
        if (strpos($path, '[') !== false) {
            $path = str_replace(['[', ']'], ['.', ''], $path);
        }

        $pathParts = explode('.', $path);
        $lastPartKey = array_key_last($pathParts);

        $isProperty = $this->hasProperty($constraintViolation->getRoot(), $pathParts[$lastPartKey]);
        $isItemOfCollection = is_numeric($pathParts[$lastPartKey]);

        if (!$isProperty && !$isItemOfCollection) {
            $pathParts[$lastPartKey] = '*';
            $path = implode('.', $pathParts);
        }

        return $path;
    }

    private function hasProperty(RestApiBundle\Mapping\RequestModel\RequestModelInterface $requestModel, string $propertyName): bool
    {
        $schema = $this->schemaResolver->resolve(get_class($requestModel));

        return isset($schema->getProperties()[$propertyName]);
    }
}
