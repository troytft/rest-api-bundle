<?php

class EntityTransformerTest extends Tests\BaseTestCase
{
    public function testSuccessFetch()
    {
        $model = new Tests\Fixture\RequestModel\EntityTransformerTest\Model();

        // by id
        $this->getRequestHandler()->handle($model, [
            'byId' => 1
        ]);

        $this->assertSame(1, $model->byId?->getId());

        // by custom field
        $this->getRequestHandler()->handle($model, [
            'bySlug' => 'design-ideas-making-house-home'
        ]);

        $this->assertSame(2, $model->bySlug?->getId());
    }

    public function testEntityNotFound()
    {
        $model = new Tests\Fixture\RequestModel\EntityTransformerTest\Model();

        // by id
        try {
            $this->getRequestHandler()->handle($model, [
                'byId' => 100404,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['byId' => ['An entity with specified value not found.']], $exception->getProperties());
        }

        // by custom field
        try {
            $this->getRequestHandler()->handle($model, [
                'bySlug' => 'invalid-slug',
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['bySlug' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testInvalidValueType()
    {
        $model = new Tests\Fixture\RequestModel\EntityTransformerTest\Model();

        // integer type
        try {
            $this->getRequestHandler()->handle($model, [
                'byId' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['byId' => ['This value should be an integer.']], $exception->getProperties());
        }

        // string type
        try {
            $this->getRequestHandler()->handle($model, [
                'bySlug' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['bySlug' => ['This value should be a string.']], $exception->getProperties());
        }
    }

    private function getRequestHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
