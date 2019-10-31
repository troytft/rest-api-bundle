<?php

namespace RestApiBundle\Manager;

use Mapper;
use RestApiBundle\Exception\RequestModelMappingException;
use RestApiBundle\Manager\RequestModel\EntityTransformer;
use RestApiBundle\RequestModelInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function array_key_last;
use function explode;
use function get_class;
use function implode;
use function sprintf;
use function str_replace;
use function strpos;
use function var_dump;

class RequestModelManager
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
     * @var EntityTransformer
     */
    private $entityTransformer;

    public function __construct(TranslatorInterface $translator, ValidatorInterface $validator, EntityTransformer $entityTransformer)
    {
        $this->mapper = new Mapper\Mapper();
        $this->translator = $translator;
        $this->validator = $validator;
        $this->entityTransformer = $entityTransformer;

        $this->mapper->addTransformer($this->entityTransformer);
    }

    /**
     * @throws RequestModelMappingException
     */
    public function handleRequest(RequestModelInterface $requestModel, array $data): void
    {
        $this->mapModel($requestModel, $data);
        $this->validateModel($requestModel);
    }

    /**
     * @throws RequestModelMappingException
     */
    private function mapModel(RequestModelInterface $requestModel, array $data): void
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

            throw new RequestModelMappingException([$path => [$message]]);
        }
    }

    /**
     * @throws RequestModelMappingException
     */
    private function validateModel(RequestModelInterface $requestModel): void
    {
        $violations = $this->validator->validate($requestModel);

        if ($violations->count()) {
            $errors = [];

            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $path = $this->getNormalizedConstraintViolationPath($violation);
                if (!isset($errors[$path])) {
                    $errors[$path] = [];
                }

                $errors[$path][] = $violation->getMessage();
            }

            throw new RequestModelMappingException($errors);
        }
    }

    private function getNormalizedConstraintViolationPath(ConstraintViolation $constraintViolation): string
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

        if (!$isProperty) {
            $pathParts[$lastPartKey] = '*';
            $path = implode('.', $pathParts);
        }

        return $path;
    }
}
