<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

use function array_merge;
use function array_values;
use function implode;

class DateTimeTransformer implements TransformerInterface
{
    public const FORMAT_OPTION = 'format';
    public const FORCE_LOCAL_TIMEZONE_OPTION = 'forceLocalTimezone';

    public function __construct(private RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
    }

    public function transform($value, array $options = [])
    {
        $format = $options[static::FORMAT_OPTION] ?? $this->settingsProvider->getDefaultRequestDateTimeFormat();
        $forceLocalTimezone = $options[static::FORCE_LOCAL_TIMEZONE_OPTION] ?? $this->settingsProvider->isForceRequestDatetimeToLocalTimezone();

        $result = \DateTime::createFromFormat($format, $value);
        if ($result === false) {
            throw new RestApiBundle\Exception\Mapper\Transformer\InvalidDateTimeFormatException($format);
        }

        $lastErrors = \DateTime::getLastErrors();
        if ($lastErrors['warning_count'] || $lastErrors['error_count']) {
            $errorMessage = implode(', ', array_merge(array_values($lastErrors['warnings']), array_values($lastErrors['errors'])));

            throw new RestApiBundle\Exception\Mapper\Transformer\InvalidDateTimeException($errorMessage);
        }

        if ($forceLocalTimezone) {
            $dateTime = new \DateTime();
            $result->setTimezone($dateTime->getTimezone());
        }

        return $result;
    }
}
