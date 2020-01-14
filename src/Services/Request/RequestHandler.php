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
     * @var Mapper\Mapper
     */
    private $mapper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var RestApiBundle\Services\SettingsProvider
     */
    private $settingsProvider;

    public function __construct(
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        RestApiBundle\Services\SettingsProvider $settingsProvider
    ) {
        $this->translator = $translator;
        $this->validator = $validator;
        $this->settingsProvider = $settingsProvider;
        $this->mapper = new Mapper\Mapper();
        $this->mapper->getSettings()
            ->setIsPropertiesNullableByDefault($this->settingsProvider->getRequestModelNullableByDefault())
            ->setIsAllowedUndefinedKeysInData($this->settingsProvider->getRequestModelAllowUndefinedKeys())
            ->setIsClearMissing($this->settingsProvider->getRequestModelClearMissingKeys());
    }

    public function addTransformer(Mapper\Transformer\TransformerInterface $transformer): void
    {
        $this->mapper->addTransformer($transformer);
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
            $this->mapper->map($requestModel, $data);
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
        $violations = $this->validator->validate($requestModel);

        if ($violations->count()) {
            $errors = [];

            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $path = $this->normalizeConstraintViolationPath($violation);
                if (!isset($errors[$path])) {
                    $errors[$path] = [];
                }

                $errors[$path][] = $violation->getMessage();
            }

            throw new RestApiBundle\Exception\RequestModelMappingException($errors);
        }
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
            ->mapper
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
