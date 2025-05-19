<?php declare(strict_types=1);

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;

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
}
