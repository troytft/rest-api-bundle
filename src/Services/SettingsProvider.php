<?php

namespace RestApiBundle\Services;

use RestApiBundle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingsProvider
{
    /**
     * @var array
     */
    private $settings;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->settings = $parameterBag->get(RestApiBundle\DependencyInjection\SettingsExtension::ALIAS);
    }

    public function isRequestPropertiesNullableByDefault(): bool
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::IS_REQUEST_PROPERTIES_NULLABLE_BY_DEFAULT];
    }

    public function isRequestUndefinedKeysAllowed(): bool
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::IS_REQUEST_UNDEFINED_KEYS_ALLOWED];
    }

    public function isRequestClearMissingEnabled(): bool
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::IS_REQUEST_CLEAR_MISSING_ENABLED];
    }

    public function isRequestValidationExceptionHandlerEnabled(): bool
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::IS_REQUEST_VALIDATION_EXCEPTION_HANDLER_ENABLED];
    }

    public function isForceRequestDatetimeToLocalTimezone(): bool
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::IS_FORCE_REQUEST_DATETIME_TO_LOCAL_TIMEZONE];
    }

    public function getDefaultRequestDatetimeFormat(): string
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATETIME_FORMAT];
    }

    public function getDefaultRequestDateFormat(): string
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATE_FORMAT];
    }
}
