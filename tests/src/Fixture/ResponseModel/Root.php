<?php

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;
use Tests;

class Root implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getDateType(): DateType
    {
        return new DateType();
    }

    public function getDateTimeType(): DateTimeType
    {
        return new DateTimeType();
    }

    public function getPhpEnumType(): PhpEnumType
    {
        return new PhpEnumType();
    }

    public function getPolyfillEnumType(): PolyfillEnumType
    {
        return new PolyfillEnumType();
    }

    public function getInnerModel(): Inner
    {
        return new Inner();
    }

    /**
     * @return Tests\Fixture\ResponseModel\Inner[]
     */
    public function getInnerModelArray(): array
    {
        return [];
    }
}
