<?php

class EntitiesCollectionTest extends Tests\BaseTestCase
{
    public function testSuccess()
    {
        $model = new TestApp\RequestModel\UpdateGenres();
        $this->getRequestModelManager()->handle($model, [
            'genres' => [1, 2]
        ]);
        $this->assertIsArray($model->getGenres());
        $this->assertCount(2, $model->getGenres());
        $this->assertTrue($model->getGenres()[0] instanceof TestApp\Entity\Genre);
        $this->assertSame($model->getGenres()[0]->getId(), 1);
        $this->assertTrue($model->getGenres()[1] instanceof TestApp\Entity\Genre);
        $this->assertSame($model->getGenres()[1]->getId(), 2);
    }

    public function testOrder()
    {
        $model = new TestApp\RequestModel\UpdateGenres();
        $this->getRequestModelManager()->handle($model, [
            'genres' => [2, 1]
        ]);
        $this->assertIsArray($model->getGenres());
        $this->assertCount(2, $model->getGenres());
        $this->assertTrue($model->getGenres()[0] instanceof TestApp\Entity\Genre);
        $this->assertSame($model->getGenres()[0]->getId(), 2);
        $this->assertTrue($model->getGenres()[1] instanceof TestApp\Entity\Genre);
        $this->assertSame($model->getGenres()[1]->getId(), 1);
    }

    public function testEntityNotFound()
    {
        try {
            $model = new TestApp\RequestModel\UpdateGenres();
            $this->getRequestModelManager()->handle($model, [
                'genres' => [1, 2, 3]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genres' => ['One entity of entities collection not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new TestApp\RequestModel\UpdateGenres();
            $this->getRequestModelManager()->handle($model, [
                'genres' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genres' => ['This value should not be null.']], $exception->getProperties());
        }
    }

    public function testInvalidItemType()
    {
        try {
            $model = new TestApp\RequestModel\UpdateGenres();
            $this->getRequestModelManager()->handle($model, [
                'genres' => [1, 'string']
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genres.1' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testRepeatableEntity()
    {
        try {
            $model = new TestApp\RequestModel\UpdateGenres();
            $this->getRequestModelManager()->handle($model, [
                'genres' => [1, 1]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['genres' => ['Values ​​should be unique.']], $exception->getProperties());
        }
    }
}
