<?php

class EntityTransformerMultipleTest extends Tests\BaseTestCase
{
    public function testSuccessFetch()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model();

        // by id
        $this->getRequestModelHandler()->handle($model, [
            'byId' => [1, 2]
        ]);

        $this->assertCount(2, $model->byId);
        $this->assertSame(1, $model->byId[0]->getId());
        $this->assertSame(2, $model->byId[1]->getId());

        // by custom field
        $this->getRequestModelHandler()->handle($model, [
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

        $this->getRequestModelHandler()->handle($model, [
            'byId' => [2, 1],
        ]);

        $this->assertSame(2, $model->byId[0]->getId());
        $this->assertSame(1, $model->byId[1]->getId());

        $this->getRequestModelHandler()->handle($model, [
            'byId' => [1, 2],
        ]);

        $this->assertSame(1, $model->byId[0]->getId());
        $this->assertSame(2, $model->byId[1]->getId());
    }

    public function testEntityNotFound()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model();

        try {
            $this->getRequestModelHandler()->handle($model, [
                'byId' => [1, 2, 3],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['byId' => ['One entity of entities collection not found.']], $exception->getProperties());
        }
    }

    public function testNotUniqueValues()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model();

        try {
            $this->getRequestModelHandler()->handle($model, [
                'byId' => [1, 1],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['byId' => ['Values should be unique.']], $exception->getProperties());
        }
    }

    private function getRequestModelHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
