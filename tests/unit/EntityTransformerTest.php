<?php

class EntityTransformerTest extends Tests\BaseTestCase
{
    public function testSuccessById()
    {
        $model = new TestApp\RequestModel\ModelWithEntityById();
        $this->getRequestModelManager()->handle($model, [
            'genre' => 1
        ]);
        $this->assertTrue($model->getGenre() instanceof TestApp\Entity\Genre);
        $this->assertSame(1, $model->getGenre()->getId());
    }

    public function testSuccessBySlug()
    {
        $model = new TestApp\RequestModel\ModelWithEntityBySlug();
        $this->getRequestModelManager()->handle($model, [
            'genre' => 'action'
        ]);
        $this->assertTrue($model->getGenre() instanceof TestApp\Entity\Genre);
        $this->assertSame('action', $model->getGenre()->getSlug());
    }

    public function testEntityNotFoundById()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestModelManager()->handle($model, [
                'genre' => 3
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genre' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testEntityNotFoundBySlug()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityBySlug();
            $this->getRequestModelManager()->handle($model, [
                'genre' => 'wrong_slug'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genre' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestModelManager()->handle($model, [
                'genre' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genre' => ['This value should not be null.']], $exception->getProperties());
        }
    }

    public function testWrongValueTypeById()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestModelManager()->handle($model, [
                'genre' => 'string'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genre' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testWrongValueTypeBySlug()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityBySlug();
            $this->getRequestModelManager()->handle($model, [
                'genre' => 10
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genre' => ['This value should be a string.']], $exception->getProperties());
        }
    }
}
