<?php

namespace RestApiBundle\Services\Docs\OpenApi;

class ExampleResolver
{
    public function getDateTime(): \DateTime
    {
        $result = new \DateTime();
        $result
            ->setTimestamp(1617885866);

        return $result;
    }
}
