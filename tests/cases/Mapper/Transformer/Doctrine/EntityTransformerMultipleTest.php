<?php

class DoctrineEntityTransformerMutipleTest extends Tests\BaseTestCase
{
    public function testSuccess()
    {
        $model = new TestApp\RequestModel\DoctrineEntityTransformerMultipleTest\Model();
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
        $model = new TestApp\RequestModel\DoctrineEntityTransformerMultipleTest\Model();
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
            $model = new TestApp\RequestModel\DoctrineEntityTransformerMultipleTest\Model();
            $this->getRequestHandler()->handle($model, [
                'books' => [1, 2, 3]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['books' => ['One entity of entities collection not found.']], $exception->getProperties());
        }
    }

    public function testRepeatableEntity()
    {
        try {
            $model = new TestApp\RequestModel\DoctrineEntityTransformerMultipleTest\Model();
            $this->getRequestHandler()->handle($model, [
                'books' => [1, 1]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['books' => ['Values should be unique.']], $exception->getProperties());
        }
    }

    private function getRequestHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
