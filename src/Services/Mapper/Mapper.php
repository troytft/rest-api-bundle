<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Mapper\DTO\Settings;
use Mapper\Exception\StackableMappingExceptionInterface;
use Mapper\Exception\StackedMappingException;
use Mapper\Exception\Transformer\TransformerExceptionInterface;
use Mapper\Exception\Transformer\WrappedTransformerException;
use Mapper\Transformer\TransformerInterface;

use function array_diff;
use function array_is_list;
use function array_keys;
use function array_merge;
use function count;
use function is_array;
use function call_user_func;

class Mapper
{
    private RestApiBundle\Model\Mapper\Settings $settings;
    private RestApiBundle\Services\Mapper\SchemaGenerator $schemaGenerator;

    /**
     * @var TransformerInterface[]
     */
    private array $transformers = [];

    public function __construct(?RestApiBundle\Model\Mapper\Settings $settings = null)
    {
        $this->settings = $settings ?: new Settings();
        $this->schemaGenerator = new SchemaGenerator($this->settings->isPropertiesNullableByDefault);
    }

    public function getSettings(): RestApiBundle\Model\Mapper\SettingsrSettings
    {
        return $this->settings;
    }

    public function setSettings(RestApiBundle\Model\Mapper\Settings $settings): void
    {
        $this->settings = $settings;
    }

    public function map(RestApiBundle\Mapping\Mapper\ModelInterface $requestModel, array $data): void
    {
        $schema = $this->schemaGenerator->getSchemaByClassInstance($requestModel);
        $this->mapObject($schema, $requestModel, $data, []);
    }

    private function mapObject(RestApiBundle\Model\Mapper\Schema\ObjectTypeInterface $schema, ModelInterface $model, array $rawValue, array $basePath): void
    {
        if ($this->settings->getIsClearMissing()) {
            $propertiesNotPresentedInData = array_diff(array_keys($schema->getProperties()), array_keys($rawValue));
            foreach ($propertiesNotPresentedInData as $propertyName) {
                $rawValue[$propertyName] = null;
            }
        }

        $mappingExceptionsStack = [];

        foreach ($rawValue as $propertyName => $propertyValue) {
            try {
                if (!isset($schema->getProperties()[$propertyName])) {
                    if ($this->settings->getIsAllowedUndefinedKeysInData()) {
                        continue;
                    } else {
                        throw new RestApiBundle\Exception\Mapper\MappingValidation\UndefinedKeyException($this->resolvePath($basePath, $propertyName));
                    }
                }

                $propertySchema = $schema->getProperties()[$propertyName];
                $this->setPropertyToModel($model, $propertyName, $propertySchema, $propertyValue, $basePath);
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

    private function setPropertyToModel(RestApiBundle\Mapping\Mapper\ModelInterface $model, string $propertyName, RestApiBundle\Model\Mapper\Schema\TypeInterface $schema, $rawValue, array $basePath): void
    {
        $value = $this->mapType($schema, $rawValue, $this->resolvePath($basePath, $propertyName));

        if ($schema->getSetterName()) {
            call_user_func([$model, $schema->getSetterName()], $value);
        } else {
            $model->$propertyName = $value;
        }
    }

    private function mapType(RestApiBundle\Model\Mapper\Schema\TypeInterface $schema, $rawValue, array $basePath)
    {
        if ($rawValue === null && $schema->getNullable()) {
            return null;
        } elseif ($rawValue === null && !$schema->getNullable()) {
            throw new RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException($basePath);
        }

        switch (true) {
            case $schema instanceof RestApiBundle\Model\Mapper\Schema\ScalarTypeInterface:
                $value = $this->mapScalarType($schema, $rawValue, $basePath);

                break;

            case $schema instanceof RestApiBundle\Model\Mapper\Schema\ObjectTypeInterface:
                $class = $schema->getClassName();
                if (!is_array($rawValue) || (count($rawValue) > 0 && array_is_list($rawValue))) {
                    throw new RestApiBundle\Exception\Mapper\MappingValidation\ObjectRequiredException($basePath);
                }
                $value = $this->mapObject($schema, new $class(), $rawValue, $basePath);

                break;

            case $schema instanceof RestApiBundle\Model\Mapper\Schema\CollectionTypeInterface:
                $value = $this->mapCollectionType($schema, $rawValue, $basePath);

                break;

            default:
                throw new \InvalidArgumentException();

        }

        if ($schema->getTransformerName()) {
            try {
                $value = $this->transformers[$schema->getTransformerName()]->transform($value, $schema->getTransformerOptions());
            } catch (TransformerExceptionInterface $transformerException) {
                throw new WrappedTransformerException($transformerException, $basePath);
            }
        }

        return $value;
    }

    private function mapScalarType(RestApiBundle\Model\Mapper\Schema\ScalarTypeInterface $schema, $rawValue, array $basePath)
    {
        return $rawValue;
    }

    private function mapCollectionType(RestApiBundle\Model\Mapper\Schema\CollectionTypeInterface $schema, $rawValue, array $basePath): array
    {
        if (!is_array($rawValue) || !array_is_list($rawValue)) {
            throw new RestApiBundle\Exception\Mapper\MappingValidation\CollectionRequiredException($basePath);
        }

        $value = [];

        foreach ($rawValue as $i => $item) {
            $value[] = $this->mapType($schema->getValuesType(), $item, $this->resolvePath($basePath, $i));
        }

        return $value;
    }

    private function resolvePath(array $basePath, $newNode): array
    {
        $path = $basePath;
        $path[] = $newNode;

        return $path;
    }

    public function addTransformer(TransformerInterface $transformer)
    {
        $this->transformers[$transformer::getName()] = $transformer;

        return $this;
    }
}
