<?php

class DoctrineEntityTransformerTest extends Tests\BaseTestCase
{
    public function testSuccessById()
    {
        $requestModel = new TestApp\RequestModel\DoctrineEntityTransformerTest\Model();
        $this->getRequestHandler()->handle($requestModel, [
            'bookById' => 1
        ]);
        $this->assertTrue($requestModel->getBookById() instanceof TestApp\Entity\Book);
        $this->assertSame(1, $requestModel->getBookById()->getId());
    }

    public function testSuccessBySlug()
    {
        $model = new TestApp\RequestModel\DoctrineEntityTransformerTest\Model();
        $this->getRequestHandler()->handle($model, [
            'bookBySlug' => 'keto-cookbook-beginners-low-carb-homemade'
        ]);
        $this->assertTrue($model->getBookBySlug() instanceof TestApp\Entity\Book);
        $this->assertSame(1, $model->getBookBySlug()->getId());
    }

    public function testEntityNotFoundById()
    {
        try {
            $requestModel = new TestApp\RequestModel\DoctrineEntityTransformerTest\Model();
            $this->getRequestHandler()->handle($requestModel, [
                'bookById' => 3
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['bookById' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testEntityNotFoundBySlug()
    {
        try {
            $requestModel = new TestApp\RequestModel\DoctrineEntityTransformerTest\Model();
            $this->getRequestHandler()->handle($requestModel, [
                'bookBySlug' => 'wrong_slug'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['bookBySlug' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testWrongValueTypeById()
    {
        try {
            $requestModel = new TestApp\RequestModel\DoctrineEntityTransformerTest\Model();
            $this->getRequestHandler()->handle($requestModel, [
                'bookById' => 'string'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['bookById' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testWrongValueTypeBySlug()
    {
        try {
            $requestModel = new TestApp\RequestModel\DoctrineEntityTransformerTest\Model();
            $this->getRequestHandler()->handle($requestModel, [
                'bookBySlug' => true,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['bookBySlug' => ['This value should be a string.']], $exception->getProperties());
        }
    }

    private function getRequestHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
