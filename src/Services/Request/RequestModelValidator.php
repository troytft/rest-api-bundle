<?php

namespace RestApiBundle\Services\Request;

use Mapper\DTO\Schema\CollectionType;
use Mapper\DTO\Schema\ObjectType;
use RestApiBundle;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function array_merge_recursive;
use function explode;
use function implode;
use function is_numeric;
use function sprintf;
use function str_replace;
use function strpos;
use function ucfirst;

class RequestModelValidator
{
    /**
     * @var RestApiBundle\Services\Request\MapperInitiator
     */
    private $mapperInitiator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        RestApiBundle\Services\Request\MapperInitiator $mapperInitiator,
        ValidatorInterface $validator
    ) {
        $this->mapperInitiator = $mapperInitiator;
        $this->validator = $validator;
    }

    /**
     * @return array<string, string[]>
     */
    public function validate(RestApiBundle\RequestModelInterface $requestModel): array
    {
        return array_merge_recursive($this->getFirstLevelErrors($requestModel), $this->getNestedErrors($requestModel));
    }

    /**
     * @return array<string, string[]>
     */
    private function getFirstLevelErrors(RestApiBundle\RequestModelInterface $requestModel): array
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
    private function getNestedErrors(RestApiBundle\RequestModelInterface $requestModel): array
    {
        $result = [];

        $schema = $this
            ->mapperInitiator
            ->getMapper()
            ->getSchemaGenerator()
            ->generate($requestModel);

        foreach ($schema->getProperties() as $propertyName => $propertyType) {
            if ($propertyType instanceof ObjectType) {
                $propertyValue = $this->getPropertyValueFromInstance($requestModel, $propertyName);
                if (!$propertyValue) {
                    continue;
                }

                $innerErrors = $this->validate($propertyValue);
                if ($innerErrors) {
                    $prefix = sprintf('%s.', $propertyName);
                    $result[] = $this->appendPrefixToArrayKeys($prefix, $innerErrors);
                }
            } elseif ($propertyType instanceof CollectionType && $propertyType->getItems() instanceof ObjectType) {
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

        return array_merge_recursive(...$result);
    }

    /**
     * @param RestApiBundle\RequestModelInterface $requestModel
     * @param string $propertyName
     *
     * @return RestApiBundle\RequestModelInterface|RestApiBundle\RequestModelInterface[]:null
     */
    private function getPropertyValueFromInstance(RestApiBundle\RequestModelInterface $instance, string $propertyName)
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

        $isProperty = $this
            ->mapperInitiator
            ->getMapper()
            ->getSchemaGenerator()
            ->isModelHasProperty($constraintViolation->getRoot(), $pathParts[$lastPartKey]);

        $isItemOfCollection = is_numeric($pathParts[$lastPartKey]);

        if (!$isProperty && !$isItemOfCollection) {
            $pathParts[$lastPartKey] = '*';
            $path = implode('.', $pathParts);
        }

        return $path;
    }
}
