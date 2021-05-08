<?php

class EntitiesCollectionTest extends Tests\BaseTestCase
{
    public function testSuccess()
    {
        $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
        $this->getRequestHandler()->handle($model, [
            'books' => [1, 2]
        ]);
        $this->assertIsArray($model->getBooks());
        $this->assertCount(2, $model->getBooks());
        $this->assertTrue($model->getBooks()[0] instanceof TestApp\Entity\Book);
        $this->assertSame($model->getBooks()[0]->getId(), 1);
        $this->assertTrue($model->getBooks()[1] instanceof TestApp\Entity\Book);
        $this->assertSame($model->getBooks()[1]->getId(), 2);
    }

    public function testOrder()
    {
        $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
        $this->getRequestHandler()->handle($model, [
            'books' => [2, 1]
        ]);

        $this->assertIsArray($model->getBooks());
        $this->assertCount(2, $model->getBooks());
        $this->assertTrue($model->getBooks()[0] instanceof TestApp\Entity\Book);
        $this->assertSame($model->getBooks()[0]->getId(), 2);
        $this->assertTrue($model->getBooks()[1] instanceof TestApp\Entity\Book);
        $this->assertSame($model->getBooks()[1]->getId(), 1);
    }

    public function testEntityNotFound()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
            $this->getRequestHandler()->handle($model, [
                'books' => [1, 2, 3]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['books' => ['One entity of entities collection not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
            $this->getRequestHandler()->handle($model, [
                'books' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['books' => ['This value should not be null.']], $exception->getProperties());
        }
    }

    public function testInvalidItemType()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
            $this->getRequestHandler()->handle($model, [
                'books' => [1, 'string']
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['books.1' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testRepeatableEntity()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
            $this->getRequestHandler()->handle($model, [
                'books' => [1, 1]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['books' => ['Values should be unique.']], $exception->getProperties());
        }
    }

    private function getRequestHandler(): RestApiBundle\Services\RequestModel\RequestHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestHandler::class);
    }
}
