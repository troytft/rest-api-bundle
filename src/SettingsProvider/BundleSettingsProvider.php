<?php

namespace RestApiBundle\SettingsProvider;

use RestApiBundle;

class BundleSettingsProvider extends RestApiBundle\SettingsProvider\BaseSettingsProvider
{
    private function getBySettingsKey(string $settingsKey)
    {
        return $this->parameterBag->get(RestApiBundle\DependencyInjection\SettingsExtension::ALIAS)[$settingsKey];
    }

    public function isRequestPropertiesNullableByDefault(): bool
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::IS_REQUEST_PROPERTIES_NULLABLE_BY_DEFAULT);
    }

    public function isRequestUndefinedKeysAllowed(): bool
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::IS_REQUEST_UNDEFINED_KEYS_ALLOWED);
    }

    public function isRequestClearMissingEnabled(): bool
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::IS_REQUEST_CLEAR_MISSING_ENABLED);
    }

    public function isRequestValidationExceptionHandlerEnabled(): bool
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::IS_REQUEST_VALIDATION_EXCEPTION_HANDLER_ENABLED);
    }

    public function isForceRequestDatetimeToLocalTimezone(): bool
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::IS_FORCE_REQUEST_DATETIME_TO_LOCAL_TIMEZONE);
    }

    public function getDefaultRequestDatetimeFormat(): string
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATETIME_FORMAT);
    }

    public function getDefaultRequestDateFormat(): string
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATE_FORMAT);
    }

    public function isResponseHandlerEnabled(): bool
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::IS_RESPONSE_HANDLER_ENABLED);
    }

    public function getResponseJsonEncodeOptions(): int
    {
        return $this->getBySettingsKey(RestApiBundle\Enum\SettingsKey::RESPONSE_JSON_ENCODE_OPTIONS);
    }
}
