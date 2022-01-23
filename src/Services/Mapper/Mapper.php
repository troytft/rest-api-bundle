<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_diff;
use function array_is_list;
use function array_keys;
use function array_merge;
use function count;
use function get_class;
use function is_array;
use function call_user_func;

class Mapper
{
    /**
     * @var RestApiBundle\Services\Mapper\Transformer\TransformerInterface[]
     */
    private array $transformers = [];

    public function __construct(
        private RestApiBundle\Services\Mapper\SchemaResolverInterface $schemaResolver,
        private TranslatorInterface $translator,
        private RestApiBundle\Services\Mapper\ModelValidator $modelValidator,
    ) {
    }

    public function map(RestApiBundle\Mapping\Mapper\ModelInterface $model, array $data, ?RestApiBundle\Model\Mapper\Context $context = null): void
    {
        try {
            $schema = $this->schemaResolver->resolve(get_class($model));
            $this->mapObject($schema, $model, $data, [], $context ?: new RestApiBundle\Model\Mapper\Context());
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            throw $this->convertStackedMappingException($exception);
        }

        $errorsStacks = $this->modelValidator->validate($model);
        if ($errorsStacks) {
            throw new RestApiBundle\Exception\Mapper\MappingException($errorsStacks);
        }
    }

    private function convertStackedMappingException(RestApiBundle\Exception\Mapper\StackedMappingException $exception): RestApiBundle\Exception\Mapper\MappingException
    {
        $errors = [];

        foreach ($exception->getExceptions() as $stackableException) {
            $translationParameters = [];

            if ($stackableException instanceof RestApiBundle\Exception\Mapper\Transformer\WrappedTransformerException) {
                $propertyPath = $stackableException->getPathAsString();
                $previousException = $stackableException->getPrevious();
                $translationId = get_class($previousException);

                if ($previousException instanceof RestApiBundle\Exception\Mapper\Transformer\InvalidDateFormatException) {
                    $translationParameters = [
                        '{format}' => $previousException->getFormat(),
                    ];
                }

                if ($previousException instanceof RestApiBundle\Exception\Mapper\Transformer\InvalidDateTimeFormatException) {
                    $translationParameters = [
                        '{format}' => $previousException->getFormat(),
                    ];
                }
            } else {
                $propertyPath = $stackableException->getPathAsString();
                $translationId = get_class($stackableException);
            }

            $message = $this->translator->trans($translationId, $translationParameters, 'exceptions');
            if ($message === $translationId) {
                throw new \InvalidArgumentException(sprintf('Can\'t find translation with key "%s"', $translationId));
            }

            $errors[$propertyPath] = [$message];
        }

        return new RestApiBundle\Exception\Mapper\MappingException($errors);
    }

    private function mapObject(RestApiBundle\Model\Mapper\Schema $schema, RestApiBundle\Mapping\Mapper\ModelInterface $model, array $data, array $basePath, RestApiBundle\Model\Mapper\Context $context): void
    {
        if ($context->clearMissing) {
            $propertiesNotPresentedInData = array_diff(array_keys($schema->properties), array_keys($data));
            foreach ($propertiesNotPresentedInData as $propertyName) {
                $data[$propertyName] = null;
            }
        }

        $mappingExceptionsStack = [];

        foreach ($data as $propertyName => $propertyValue) {
            try {
                if (!isset($schema->properties[$propertyName])) {
                    throw new RestApiBundle\Exception\Mapper\MappingValidation\UndefinedKeyException($this->resolvePath($basePath, $propertyName));
                }

                $propertySchema = $schema->properties[$propertyName];
                if (!$propertySchema instanceof RestApiBundle\Model\Mapper\Schema) {
                    throw new \LogicException();
                }

                $value = $this->mapType($propertySchema, $propertyValue, $this->resolvePath($basePath, $propertyName), $context);

                if ($propertySchema->propertySetterName) {
                    call_user_func([$model, $propertySchema->propertySetterName], $value);
                } else {
                    $model->$propertyName = $value;
                }
            } catch (RestApiBundle\Exception\Mapper\StackableMappingExceptionInterface $exception) {
                $mappingExceptionsStack[] = $exception;
            } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
                $mappingExceptionsStack = array_merge($mappingExceptionsStack, $exception->getExceptions());
            }
        }

        if ($mappingExceptionsStack) {
            throw new RestApiBundle\Exception\Mapper\StackedMappingException($mappingExceptionsStack);
        }
    }

    private function mapType(RestApiBundle\Model\Mapper\Schema $schema, $rawValue, array $basePath, RestApiBundle\Model\Mapper\Context $context)
    {
        if ($rawValue === null && $schema->isNullable) {
            return null;
        } elseif ($rawValue === null && !$schema->isNullable) {
            throw new RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException($basePath);
        }

        switch ($schema->type) {
            case RestApiBundle\Model\Mapper\Schema::TRANSFORMER_AWARE_TYPE:
                $value = $this->mapTransformerAwareType($schema, $rawValue, $basePath);

                break;

            case RestApiBundle\Model\Mapper\Schema::MODEL_TYPE:
                $class = $schema->class;
                if (!is_array($rawValue) || (count($rawValue) > 0 && array_is_list($rawValue))) {
                    throw new RestApiBundle\Exception\Mapper\MappingValidation\ObjectRequiredException($basePath);
                }

                $value = new $class();
                $this->mapObject($schema, $value, $rawValue, $basePath, $context);

                break;

            case RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE:
                $value = $this->mapCollectionType($schema, $rawValue, $basePath, $context);

                break;

            default:
                throw new \InvalidArgumentException();

        }

        return $value;
    }

    private function mapTransformerAwareType(RestApiBundle\Model\Mapper\Schema $schema, $rawValue, array $basePath)
    {
        try {
            return $this->transformers[$schema->transformerClass]->transform($rawValue, $schema->transformerOptions);
        } catch (RestApiBundle\Exception\Mapper\Transformer\TransformerExceptionInterface $transformerException) {
            throw new RestApiBundle\Exception\Mapper\Transformer\WrappedTransformerException($transformerException, $basePath);
        }
    }

    private function mapCollectionType(RestApiBundle\Model\Mapper\Schema $schema, $rawValue, array $basePath, RestApiBundle\Model\Mapper\Context $context): array
    {
        if (!is_array($rawValue) || !array_is_list($rawValue)) {
            throw new RestApiBundle\Exception\Mapper\MappingValidation\CollectionRequiredException($basePath);
        }

        $value = [];

        foreach ($rawValue as $i => $item) {
            $value[] = $this->mapType($schema->valuesType, $item, $this->resolvePath($basePath, $i), $context);
        }

        return $value;
    }

    private function resolvePath(array $basePath, $newNode): array
    {
        $path = $basePath;
        $path[] = $newNode;

        return $path;
    }

    public function addTransformer(RestApiBundle\Services\Mapper\Transformer\TransformerInterface $transformer): static
    {
        $this->transformers[get_class($transformer)] = $transformer;

        return $this;
    }
}
