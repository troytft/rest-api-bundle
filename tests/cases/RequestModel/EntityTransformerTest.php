<?php declare(strict_types=1);

class EntityTransformerTest extends Tests\BaseTestCase
{
    public function testSuccessFetch()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerTest\Model();

        // by id
        $this->getMapper()->map($model, [
            'byId' => 1
        ]);

        $this->assertSame(1, $model->byId?->getId());

        // by custom field
        $this->getMapper()->map($model, [
            'bySlug' => 'design-ideas-making-house-home'
        ]);

        $this->assertSame(2, $model->bySlug?->getId());
    }

    public function testEntityNotFound()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerTest\Model();

        // by id
        try {
            $this->getMapper()->map($model, [
                'byId' => 100404,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['byId' => ['An entity with specified value not found.']], $exception->getProperties());
        }

        // by custom field
        try {
            $this->getMapper()->map($model, [
                'bySlug' => 'invalid-slug',
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['bySlug' => ['An entity with specified value not found.']], $exception->getProperties());
        }
    }

    public function testInvalidValueType()
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerTest\Model();

        // integer type
        try {
            $this->getMapper()->map($model, [
                'byId' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['byId' => ['This value should be an integer.']], $exception->getProperties());
        }

        // string type
        try {
            $this->getMapper()->map($model, [
                'bySlug' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['bySlug' => ['This value should be a string.']], $exception->getProperties());
        }
    }
}
