<?php

namespace Tests;

use RestApiBundle;
use Tests;
use Tests\DemoApp\DemoBundle\Entity\File;

class EntityTransformerTest extends BaseBundleTestCase
{
    public function testSuccess()
    {
        $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithEntity();
        $this->getRequestModelManager()->handleRequest($model, [
            'fieldWithEntity' => 1
        ]);
        $this->assertTrue($model->getFieldWithEntity() instanceof File);
        $this->assertSame(1, $model->getFieldWithEntity()->getId());
    }

    public function testEntityNotFound()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithEntity();
            $this->getRequestModelManager()->handleRequest($model, [
                'fieldWithEntity' => 3
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithEntity();
            $this->getRequestModelManager()->handleRequest($model, [
                'fieldWithEntity' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['This value should be scalar.']], $exception->getProperties());
        }
    }
}
