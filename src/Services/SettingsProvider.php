<?php

declare(strict_types=1);

namespace RestApiBundle\Services;

use RestApiBundle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingsProvider
{
    private ParameterBagInterface $parameterBag;

    /**
     * @var array<string, int|float|bool|string>
     */
    private array $settings;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;

        /** @var array<string, int|float|bool|string> $settings */
        $settings = $parameterBag->get(RestApiBundle\DependencyInjection\SettingsExtension::ALIAS);
        $this->settings = $settings;
    }

    public function isRequestValidationExceptionHandlerEnabled(): bool
    {
        return (bool) $this->settings[RestApiBundle\Enum\SettingsKey::IS_REQUEST_VALIDATION_EXCEPTION_HANDLER_ENABLED];
    }

    public function isForceRequestDatetimeToLocalTimezone(): bool
    {
        return (bool) $this->settings[RestApiBundle\Enum\SettingsKey::IS_FORCE_REQUEST_DATETIME_TO_LOCAL_TIMEZONE];
    }

    public function getDefaultRequestDateTimeFormat(): string
    {
        return (string) $this->settings[RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATETIME_FORMAT];
    }

    public function getDefaultRequestDateFormat(): string
    {
        return (string) $this->settings[RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATE_FORMAT];
    }

    public function isResponseHandlerEnabled(): bool
    {
        return (bool) $this->settings[RestApiBundle\Enum\SettingsKey::IS_RESPONSE_HANDLER_ENABLED];
    }

    public function getResponseJsonEncodeOptions(): int
    {
        return (int) $this->settings[RestApiBundle\Enum\SettingsKey::RESPONSE_JSON_ENCODE_OPTIONS];
    }

    public function getResponseModelDateFormat(): string
    {
        return (string) $this->settings[RestApiBundle\Enum\SettingsKey::RESPONSE_MODEL_DATE_FORMAT];
    }

    public function getResponseModelDateTimeFormat(): string
    {
        return \DATE_ATOM;
    }

    public function getSourceCodeDirectory(): string
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        \assert(\is_string($projectDir));

        return $projectDir . '/src';
    }
}
