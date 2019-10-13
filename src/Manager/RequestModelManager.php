<?php

namespace RestApiBundle\Manager;

use Mapper;
use RestApiBundle\Exception\RequestModelMappingException;
use RestApiBundle\RequestModelInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;
use function explode;
use function get_class;
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

    public function __construct(TranslatorInterface $translator, ValidatorInterface $validator)
    {
        $this->mapper = new Mapper\Mapper();
        $this->translator = $translator;
        $this->validator = $validator;
    }

    public function handleRequest(RequestModelInterface $requestModel, array $data): void
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
                } elseif ($previousException instanceof Mapper\Exception\Transformer\InvalidDateTimeFormatException) {
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

        $violations = $this->validator->validate($requestModel);

        if (count($violations)) {
            $errors = [];

            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                if (!isset($errors[$violation->getPropertyPath()])) {
                    $errors[$violation->getPropertyPath()] = [];
                }

                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }

            foreach ($errors as $path => $pathErrors) {
                if (strpos($path, '[') !== false) {
                    $newPath = str_replace(['[', ']'], ['.', ''], $path);
                    $errors[$newPath] = $pathErrors;
                    unset($errors[$path]);
                }
            }

            throw new RequestModelMappingException($errors);
        }
    }
}
