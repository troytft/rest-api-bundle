<?php

class EntitiesCollectionTest extends Tests\BaseTestCase
{
    public function testSuccess()
    {
        $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
        $this->getRequestModelManager()->handle($model, [
            'fieldWithCollectionOfEntities' => [1, 2]
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
        $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
        $this->getRequestModelManager()->handle($model, [
            'fieldWithCollectionOfEntities' => [2, 1]
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
            $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithCollectionOfEntities' => [1, 2, 3]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithCollectionOfEntities' => ['One entity of entities collection not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithCollectionOfEntities' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithCollectionOfEntities' => ['This value should not be null.']], $exception->getProperties());
        }
    }

    public function testInvalidItemType()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithCollectionOfEntities' => [1, 'string']
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithCollectionOfEntities.1' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testRepeatableEntity()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithArrayOfEntities();
            $this->getRequestModelManager()->handle($model, [
                'fieldWithCollectionOfEntities' => [1, 1]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithCollectionOfEntities' => ['Values ​​should be unique.']], $exception->getProperties());
        }
    }
}
