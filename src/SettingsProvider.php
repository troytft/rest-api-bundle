<?php

namespace RestApiBundle;

use RestApiBundle\DependencyInjection\ConfigExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingsProvider
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getRequestModelNullableByDefault(): bool
    {
        return $this->parameterBag->get(ConfigExtension::PARAMETER_REQUEST_MODEL_NULLABLE_BY_DEFAULT);
    }

    public function getRequestModelAllowUndefinedKeys(): bool
    {
        return $this->parameterBag->get(ConfigExtension::PARAMETER_REQUEST_MODEL_ALLOW_UNDEFINED_KEYS);
    }

    public function getRequestModelClearMissingKeys(): bool
    {
        return $this->parameterBag->get(ConfigExtension::PARAMETER_REQUEST_MODEL_CLEAR_MISSING);
    }

    public function getRequestModelHandleException(): bool
    {
        return $this->parameterBag->get(ConfigExtension::PARAMETER_REQUEST_MODEL_HANDLE_EXCEPTION);
    }

    public function getRequestModelDateTimeTransformerForceLocalTimezone(): bool
    {
        return $this->parameterBag->get(ConfigExtension::PARAMETER_REQUEST_MODEL_DATE_TIME_TRANSFORMER_FORCE_LOCAL_TIMEZONE);
    }

    public function getRequestModelDateTimeTransformerDefaultFormat(): string
    {
        return $this->parameterBag->get(ConfigExtension::PARAMETER_REQUEST_MODEL_DATE_TIME_TRANSFORMER_DEFAULT_FORMAT);
    }

    public function getRequestModelDateTransformerDefaultFormat(): string
    {
        return $this->parameterBag->get(ConfigExtension::PARAMETER_REQUEST_MODEL_DATE_TRANSFORMER_DEFAULT_FORMAT);
    }
}
