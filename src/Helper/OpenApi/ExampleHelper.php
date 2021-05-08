<?php

namespace RestApiBundle\Helper\OpenApi;

class ExampleHelper
{
    public static function getExampleDate(): \DateTime
    {
        $result = new \DateTime();
        $result
            ->setTimestamp(1617885866)
            ->setTimezone(new \DateTimeZone('Europe/Prague'));

        return $result;
    }
}
