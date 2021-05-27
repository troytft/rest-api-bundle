<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

class DateTransformer extends \Mapper\Transformer\DateTransformer
{
    public const FORMAT_OPTION_NAME = 'format';

    private RestApiBundle\Services\SettingsProvider $settingsProvider;

    public function __construct(RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;
    }

    public function transform($value, array $options = [])
    {
        if (!isset($options[static::FORMAT_OPTION_NAME])) {
            $options[static::FORMAT_OPTION_NAME] = $this->settingsProvider->getDefaultRequestDateFormat();
        }

        return parent::transform($value, $options);
    }

    public static function getName(): string
    {
        return \Mapper\Transformer\DateTransformer::getName();
    }
}
