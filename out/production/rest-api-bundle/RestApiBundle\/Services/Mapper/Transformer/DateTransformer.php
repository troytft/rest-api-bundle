<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

use function array_merge;
use function array_values;
use function implode;

class DateTransformer implements TransformerInterface
{
    public const FORMAT_OPTION = 'format';

    private RestApiBundle\Services\SettingsProvider $settingsProvider;

    public function __construct(RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;
    }

    public function transform($value, array $options = [])
    {
        $format = $options[static::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateFormat();

        $result = \DateTime::createFromFormat($format, $value);
        if ($result === false) {
            throw new RestApiBundle\Exception\Mapper\Transformer\InvalidDateFormatException($format);
        }

        $lastErrors = \DateTime::getLastErrors();
        if ($lastErrors['warning_count'] || $lastErrors['error_count']) {
            $errorMessage = implode(', ', array_merge(array_values($lastErrors['warnings']), array_values($lastErrors['errors'])));

            throw new RestApiBundle\Exception\Mapper\Transformer\InvalidDateException($errorMessage);
        }

        $result->setTime(0, 0);

        return $result;
    }
}
