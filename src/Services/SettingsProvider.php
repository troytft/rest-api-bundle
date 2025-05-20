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
        $this->settings = $parameterBag->get(RestApiBundle\DependencyInjection\SettingsExtension::ALIAS);
    }

    public function isRequestValidationExceptionHandlerEnabled(): bool
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::IS_REQUEST_VALIDATION_EXCEPTION_HANDLER_ENABLED];
    }

    public function isForceRequestDatetimeToLocalTimezone(): bool
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::IS_FORCE_REQUEST_DATETIME_TO_LOCAL_TIMEZONE];
    }

    public function getDefaultRequestDateTimeFormat(): string
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATETIME_FORMAT];
    }

    public function getDefaultRequestDateFormat(): string
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATE_FORMAT];
    }

    public function isResponseHandlerEnabled(): bool
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::IS_RESPONSE_HANDLER_ENABLED];
    }

    public function getResponseJsonEncodeOptions(): int
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::RESPONSE_JSON_ENCODE_OPTIONS];
    }

    public function getResponseModelDateFormat(): string
    {
        return $this->settings[RestApiBundle\Enum\SettingsKey::RESPONSE_MODEL_DATE_FORMAT];
    }

    public function getResponseModelDateTimeFormat(): string
    {
        return \DATE_ATOM;
    }

    public function getSourceCodeDirectory(): string
    {
        return $this->parameterBag->get('kernel.project_dir') . '/src';
    }
}
