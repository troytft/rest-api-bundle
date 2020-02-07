<?php

namespace RestApiBundle\Services\Request;

use RestApiBundle;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function explode;
use function implode;
use function is_numeric;
use function str_replace;
use function strpos;

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
     * @return string[]
     */
    public function validate(RestApiBundle\RequestModelInterface $requestModel): array
    {
        $errors = [];
        $violations = $this->validator->validate($requestModel);

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $path = $this->normalizeConstraintViolationPath($violation);
            if (!isset($errors[$path])) {
                $errors[$path] = [];
            }

            $errors[$path][] = $violation->getMessage();
        }

        return $errors;
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
