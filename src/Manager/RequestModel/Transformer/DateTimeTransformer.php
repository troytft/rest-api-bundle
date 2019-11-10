<?php

namespace RestApiBundle\Manager\RequestModel\Transformer;

use RestApiBundle\HelperService\SettingsProvider;

class DateTimeTransformer extends \Mapper\Transformer\DateTimeTransformer
{
    /**
     * @var SettingsProvider
     */
    private $settingsProvider;

    public function __construct(SettingsProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;
    }

    public function transform($value, array $options = [])
    {
        if (!isset($options[static::FORMAT_OPTION_NAME])) {
            $options[static::FORMAT_OPTION_NAME] = $this->settingsProvider->getRequestModelDateTimeTransformerDefaultFormat();
        }

        if (!isset($options[static::FORCE_LOCAL_TIMEZONE_OPTION_NAME])) {
            $options[static::FORCE_LOCAL_TIMEZONE_OPTION_NAME] = $this->settingsProvider->getRequestModelDateTimeTransformerForceLocalTimezone();
        }

        return parent::transform($value, $options);
    }

    public static function getName(): string
    {
        return \Mapper\Transformer\DateTimeTransformer::getName();
    }
}
