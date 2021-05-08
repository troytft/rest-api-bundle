<?php

namespace RestApiBundle\Services\RequestModel\MapperTransformer;

use RestApiBundle;

class DateTimeTransformer extends \Mapper\Transformer\DateTimeTransformer
{
    /**
     * @var RestApiBundle\Services\SettingsProvider
     */
    private $settingsProvider;

    public function __construct(RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;
    }

    public function transform($value, array $options = [])
    {
        if (!isset($options[static::FORMAT_OPTION_NAME])) {
            $options[static::FORMAT_OPTION_NAME] = $this->settingsProvider->getDefaultRequestDatetimeFormat();
        }

        if (!isset($options[static::FORCE_LOCAL_TIMEZONE_OPTION_NAME])) {
            $options[static::FORCE_LOCAL_TIMEZONE_OPTION_NAME] = $this->settingsProvider->isForceRequestDatetimeToLocalTimezone();
        }

        return parent::transform($value, $options);
    }

    public static function getName(): string
    {
        return \Mapper\Transformer\DateTimeTransformer::getName();
    }
}
