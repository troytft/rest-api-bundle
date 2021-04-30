<?php

class EntityTransformerTest extends Tests\BaseTestCase
{
    public function testSuccessById()
    {
        $model = new TestApp\RequestModel\ModelWithEntityById();
        $this->getRequestHandler()->handle($model, [
            'book' => 1
        ]);
        $this->assertTrue($model->getBook() instanceof TestApp\Entity\Book);
        $this->assertSame(1, $model->getBook()->getId());
    }

    public function testSuccessBySlug()
    {
        $model = new TestApp\RequestModel\ModelWithEntityBySlug();
        $this->getRequestHandler()->handle($model, [
            'book' => 'keto-cookbook-beginners-low-carb-homemade'
        ]);
        $this->assertTrue($model->getBook() instanceof TestApp\Entity\Book);
        $this->assertSame(1, $model->getBook()->getId());
    }

    public function testEntityNotFoundById()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestHandler()->handle($model, [
                'book' => 3
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['book' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testEntityNotFoundBySlug()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityBySlug();
            $this->getRequestHandler()->handle($model, [
                'book' => 'wrong_slug'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['book' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestHandler()->handle($model, [
                'book' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['book' => ['This value should not be null.']], $exception->getProperties());
        }
    }

    public function testWrongValueTypeById()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityById();
            $this->getRequestHandler()->handle($model, [
                'book' => 'string'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['book' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testWrongValueTypeBySlug()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithEntityBySlug();
            $this->getRequestHandler()->handle($model, [
                'book' => 10
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['book' => ['This value should be a string.']], $exception->getProperties());
        }
    }

    private function getRequestHandler(): RestApiBundle\Services\Request\RequestHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\Request\RequestHandler::class);
    }
}
