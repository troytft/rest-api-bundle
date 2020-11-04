<?php

namespace Tests\TestCase\Services\OpenApi;

use Mapper;
use RestApiBundle;
use Tests;

class RequestModelHelperTest extends Tests\TestCase\BaseTestCase
{
    public function testDateTimeTransformer()
    {
        $mapperSchema = new Mapper\DTO\Schema\ScalarType();
        $mapperSchema
            ->setNullable(false)
            ->setTransformerName(Mapper\Transformer\DateTimeTransformer::getName());

        /** @var RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface $docsSchema */
        $docsSchema = $this->invokePrivateMethod($this->getRequestModelHelper(), 'convert', [$mapperSchema]);

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\DateTimeType::class, $docsSchema);
        $this->assertFalse($docsSchema->getNullable());
    }

    public function testDateTransformer()
    {
        $mapperSchema = new Mapper\DTO\Schema\ScalarType();
        $mapperSchema
            ->setNullable(false)
            ->setTransformerName(Mapper\Transformer\DateTransformer::getName());

        /** @var RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface $docsSchema */
        $docsSchema = $this->invokePrivateMethod($this->getRequestModelHelper(), 'convert', [$mapperSchema]);

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\DateType::class, $docsSchema);
        $this->assertFalse($docsSchema->getNullable());
    }

    public function testEntityTransformer()
    {
        $mapperSchema = new Mapper\DTO\Schema\ScalarType();
        $mapperSchema
            ->setNullable(false)
            ->setTransformerName(RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::getName())
            ->setTransformerOptions([
                RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::CLASS_OPTION => Tests\TestApp\TestBundle\Entity\Genre::class,
                RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::FIELD_OPTION => 'slug',
            ]);

        /** @var RestApiBundle\DTO\OpenApi\Schema\StringType $docsSchema */
        $docsSchema = $this->invokePrivateMethod($this->getRequestModelHelper(), 'convert', [$mapperSchema]);

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $docsSchema);
        $this->assertSame('Entity "Genre" by field "slug"', $docsSchema->getDescription());
        $this->assertFalse($docsSchema->getNullable());
    }

    public function testEntitiesCollectionTransformer()
    {
        $mapperSchema = new Mapper\DTO\Schema\ScalarType();
        $mapperSchema
            ->setNullable(false)
            ->setTransformerName(RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::getName())
            ->setTransformerOptions([
                RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::CLASS_OPTION => Tests\TestApp\TestBundle\Entity\Genre::class,
                RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::FIELD_OPTION => 'slug',
            ]);

        /** @var RestApiBundle\DTO\OpenApi\Schema\ArrayType $docsSchema */
        $docsSchema = $this->invokePrivateMethod($this->getRequestModelHelper(), 'convert', [$mapperSchema]);

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\ArrayType::class, $docsSchema);
        $this->assertFalse($docsSchema->getNullable());

        $innerType = $docsSchema->getInnerType();
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $innerType);
        $this->assertFalse($innerType->getNullable());
    }
}
