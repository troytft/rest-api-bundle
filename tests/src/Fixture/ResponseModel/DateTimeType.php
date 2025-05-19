<?php declare(strict_types=1);

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;

class DateTimeType implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getRequired(): \DateTime
    {
        return new \DateTime('2025-05-19 00:00:00');
    }

    public function getNullable(): ?\DateTime
    {
        return null;
    }
}
