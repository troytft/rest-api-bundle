<?php

namespace RestApiBundle\Services\Request\MapperTransformer;

use RestApiBundle;

class DateTransformer extends \Mapper\Transformer\DateTransformer
{
    /**
     * @var RestApiBundle\SettingsProvider\BundleSettingsProvider
     */
    private $settingsProvider;

    public function __construct(RestApiBundle\SettingsProvider\BundleSettingsProvider $settingsProvider)
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
