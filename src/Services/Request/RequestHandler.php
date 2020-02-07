<?php

namespace RestApiBundle\Services\Request;

use Mapper;
use RestApiBundle;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function array_key_last;
use function explode;
use function get_class;
use function implode;
use function sprintf;
use function str_replace;
use function strpos;

class RequestHandler
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var RestApiBundle\Services\Request\MapperInitiator
     */
    private $mapperInitiator;

    /**
     * @var RestApiBundle\Services\SettingsProvider
     */
    private $settingsProvider;

    public function __construct(
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        RestApiBundle\Services\Request\MapperInitiator $mapperInitiator,
        RestApiBundle\Services\SettingsProvider $settingsProvider
    ) {
        $this->translator = $translator;
        $this->validator = $validator;
        $this->mapperInitiator = $mapperInitiator;
        $this->settingsProvider = $settingsProvider;
    }

    /**
     * @throws RestApiBundle\Exception\RequestModelMappingException
     */
    public function handle(RestApiBundle\RequestModelInterface $requestModel, array $data): void
    {
        $this->map($requestModel, $data);
        $this->validate($requestModel);
    }

    /**
     * @throws RestApiBundle\Exception\RequestModelMappingException
     */
    private function map(RestApiBundle\RequestModelInterface $requestModel, array $data): void
    {
        try {
            $this->mapperInitiator->getMapper()->map($requestModel, $data);
        } catch (Mapper\Exception\ExceptionInterface $exception) {
            $translationParameters = [];

            if ($exception instanceof Mapper\Exception\MappingValidation\MappingValidationExceptionInterface || $exception instanceof Mapper\Exception\MappingValidation\UndefinedKeyException) {
                $path = $exception->getPathAsString();
                $translationId = get_class($exception);
            } elseif ($exception instanceof Mapper\Exception\Transformer\WrappedTransformerException) {
                $path = $exception->getPathAsString();
                $previousException = $exception->getPrevious();
                $translationId = get_class($previousException);

                if ($previousException instanceof Mapper\Exception\Transformer\InvalidDateFormatException) {
                    $translationParameters = [
                        '{format}' => $previousException->getFormat(),
                    ];
                }

                if ($previousException instanceof Mapper\Exception\Transformer\InvalidDateTimeFormatException) {
                    $translationParameters = [
                        '{format}' => $previousException->getFormat(),
                    ];
                }
            } else {
                throw $exception;
            }

            $message = $this->translator->trans($translationId, $translationParameters, 'exceptions');

            if ($message === $translationId) {
                throw new \InvalidArgumentException(sprintf('Can\'t find translation with key "%s"', $translationId));
            }

            throw new RestApiBundle\Exception\RequestModelMappingException([$path => [$message]]);
        }
    }

    /**
     * @throws RestApiBundle\Exception\RequestModelMappingException
     */
    private function validate(RestApiBundle\RequestModelInterface $requestModel): void
    {
        $errors = $this->getValidationErrorsByRequestModel($requestModel);

        if ($errors) {
            throw new RestApiBundle\Exception\RequestModelMappingException($errors);
        }
    }

    private function getValidationErrorsByRequestModel(RestApiBundle\RequestModelInterface $requestModel): array
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
