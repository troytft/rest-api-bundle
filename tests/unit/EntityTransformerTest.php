<?php

class EntityTransformerTest extends Tests\BaseTestCase
{
    public function testSuccessById()
    {
        $model = new TestApp\RequestModel\ModelWithEntityById();
        $this->getRequestModelManager()->handle($model, [
            'fieldWithEntity' => 1
        ]);
        $this->assertTrue($model->getFieldWithEntity() instanceof TestApp\Entity\Genre);
        $this->assertSame(1, $model->getFieldWithEntity()->getId());
    }

    public function testSuccessBySlug()
    {
        $model = new TestApp\RequestModel\ModelWithEntityBySlug();
        $this->getRequestModelManager()->handle($model, [
            'fieldWithEntity' => 'action'
        ]);
        $this->assertTrue($model->getFieldWithEntity() instanceof TestApp\Entity\Genre);
        $this->assertSame('action', $model->getFieldWithEntity()->getSlug());
    }

    public function testEntityNotFoundById()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithEntity' => 3
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testEntityNotFoundBySlug()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityBySlug();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithEntity' => 'wrong_slug'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithEntity' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['This value should not be null.']], $exception->getProperties());
        }
    }

    public function testWrongValueTypeById()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithEntity' => 'string'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testWrongValueTypeBySlug()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityBySlug();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithEntity' => 10
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithEntity' => ['This value should be a string.']], $exception->getProperties());
        }
    }
}
