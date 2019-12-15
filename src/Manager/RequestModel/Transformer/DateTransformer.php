<?php

namespace RestApiBundle\Manager\RequestModel\Transformer;

use RestApiBundle;

class DateTransformer extends \Mapper\Transformer\DateTransformer
{
    /**
     * @var RestApiBundle\SettingsProvider
     */
    private $settingsProvider;

    public function __construct(RestApiBundle\SettingsProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;
    }

    public function transform($value, array $options = [])
    {
        if (!isset($options[static::FORMAT_OPTION_NAME])) {
            $options[static::FORMAT_OPTION_NAME] = $this->settingsProvider->getRequestModelDateTransformerDefaultFormat();
        }

        return parent::transform($value, $options);
    }

    public static function getName(): string
    {
        return \Mapper\Transformer\DateTransformer::getName();
    }
}
