<?php

class EntityTransformerMultipleTest extends Tests\BaseTestCase
{
    public function testSuccessFetch()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model();

        // by id
        $this->getMapper()->map($model, [
            'byId' => [1, 2]
        ]);

        $this->assertCount(2, $model->byId);
        $this->assertSame(1, $model->byId[0]->getId());
        $this->assertSame(2, $model->byId[1]->getId());

        // by custom field
        $this->getMapper()->map($model, [
            'bySlug' => [
                'keto-cookbook-beginners-low-carb-homemade',
                'design-ideas-making-house-home',
            ]
        ]);

        $this->assertCount(2, $model->bySlug);
        $this->assertSame(1, $model->bySlug[0]->getId());
        $this->assertSame(2, $model->bySlug[1]->getId());
    }

    public function testOrder()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model();

        $this->getMapper()->map($model, [
            'byId' => [2, 1],
        ]);

        $this->assertSame(2, $model->byId[0]->getId());
        $this->assertSame(1, $model->byId[1]->getId());

        $this->getMapper()->map($model, [
            'byId' => [1, 2],
        ]);

        $this->assertSame(1, $model->byId[0]->getId());
        $this->assertSame(2, $model->byId[1]->getId());
    }

    public function testEntityNotFound()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model();

        try {
            $this->getMapper()->map($model, [
                'byId' => [1, 2, 3],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['byId' => ['One entity of entities collection not found.']], $exception->getProperties());
        }
    }

    public function testNotUniqueValues()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model();

        try {
            $this->getMapper()->map($model, [
                'byId' => [1, 1],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['byId' => ['Values should be unique.']], $exception->getProperties());
        }
    }
}
