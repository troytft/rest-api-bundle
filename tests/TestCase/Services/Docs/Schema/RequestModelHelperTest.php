<?php

namespace Tests\TestCase\Services\Docs\Schema;

use Mapper;
use RestApiBundle;
use Tests;

class RequestModelHelperTest extends Tests\TestCase\BaseTestCase
{
    public function testConvertDateTimeTransformer()
    {
        $mapperSchema = new Mapper\DTO\Schema\ScalarType();
        $mapperSchema
            ->setNullable(false)
            ->setTransformerName(Mapper\Transformer\DateTimeTransformer::getName());

        /** @var RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface $docsSchema */
        $docsSchema = $this->invokePrivateMethod($this->getRequestModelHelper(), 'convert', [$mapperSchema]);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\DateTimeType::class, $docsSchema);
        $this->assertFalse($docsSchema->getNullable());
    }

    public function testConvertDateTransformer()
    {
        $mapperSchema = new Mapper\DTO\Schema\ScalarType();
        $mapperSchema
            ->setNullable(false)
            ->setTransformerName(Mapper\Transformer\DateTransformer::getName());

        /** @var RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface $docsSchema */
        $docsSchema = $this->invokePrivateMethod($this->getRequestModelHelper(), 'convert', [$mapperSchema]);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\DateType::class, $docsSchema);
        $this->assertFalse($docsSchema->getNullable());
    }
}
