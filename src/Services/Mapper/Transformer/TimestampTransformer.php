<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

class TimestampTransformer extends IntegerTransformer
{
    public function transform($value, array $options = [])
    {
        $timestamp = parent::transform($value, []);

        $result = new RestApiBundle\Mapping\Mapper\Timestamp();
        $result->setTimestamp($timestamp);

        return $result;
    }
}
