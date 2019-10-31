<?php

namespace Tests;

use RestApiBundle;
use Tests;
use Tests\Mock\DemoBundle\Entity\File;

class EntityTransformerTest extends BaseBundleTestCase
{
    public function testSuccessTransform()
    {
        $model = new Tests\Mock\DemoBundle\RequestModel\ModelWithEntity();
        $this->getRequestModelManager()->handleRequest($model, [
            'fieldWithEntity' => 1
        ]);
        $this->assertTrue($model->getFieldWithEntity() instanceof File);
    }

    public function testEntityNotFound()
    {
        try {
            $model = new Tests\Mock\DemoBundle\RequestModel\ModelWithEntity();
            $this->getRequestModelManager()->handleRequest($model, [
                'fieldWithEntity' => 2
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new Tests\Mock\DemoBundle\RequestModel\ModelWithEntity();
            $this->getRequestModelManager()->handleRequest($model, [
                'fieldWithEntity' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['This value should be scalar.']], $exception->getProperties());
        }
    }
}
