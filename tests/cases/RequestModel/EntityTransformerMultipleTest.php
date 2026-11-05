<?php

class EntityTransformerMultipleTest extends Tests\BaseTestCase
{
    public function testSuccessFetch(): void
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

    public function testInvalidCollection(): void
    {
        $model = new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model();

        try {
            $this->getMapper()->map($model, [
                'byId' => 13,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['byId' => ['This value should be a collection.']], $exception->getProperties());
        }

        try {
            $this->getMapper()->map($model, [
                'byId' => [null,],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['byId.0' => ['This value should not be null.']], $exception->getProperties());
        }

        try {
            $this->getMapper()->map($model, [
                'bySlug' => 'keto-cookbook-beginners-low-carb-homemade',
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['bySlug' => ['This value should be a collection.']], $exception->getProperties());
        }

        try {
            $this->getMapper()->map($model, [
                'bySlug' => [null,],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['bySlug.0' => ['This value should not be null.']], $exception->getProperties());
        }
    }

    public function testInvalidNonFirstItemById(): void
    {
        $cases = [
            ['1abc', 'This value should be an integer.'],
            [1.9, 'This value should be an integer.'],
            [true, 'This value should be an integer.'],
            [[5], 'This value should be an integer.'],
            [null, 'This value should not be null.'],
        ];

        foreach ($cases as [$item, $message]) {
            try {
                $this->getMapper()->map(new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model(), [
                    'byId' => [2, $item],
                ]);
                $this->fail();
            } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
                $this->assertSame(['byId.1' => [$message]], $exception->getProperties());
            }
        }
    }

    public function testInvalidNonFirstItemBySlug(): void
    {
        $cases = [
            [['x'], 'This value should be a string.'],
            [null, 'This value should not be null.'],
        ];

        foreach ($cases as [$item, $message]) {
            try {
                $this->getMapper()->map(new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model(), [
                    'bySlug' => ['keto-cookbook-beginners-low-carb-homemade', $item],
                ]);
                $this->fail();
            } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
                $this->assertSame(['bySlug.1' => [$message]], $exception->getProperties());
            }
        }
    }

    public function testObjectInsteadOfList(): void
    {
        try {
            $this->getMapper()->map(new Tests\Fixture\Mapper\EntityTransformerMultipleTest\Model(), [
                'byId' => [0 => 1, 'x' => 2],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['byId' => ['This value should be a collection.']], $exception->getProperties());
        }
    }

    public function testOrder(): void
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

    public function testEntityNotFound(): void
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

    public function testNotUniqueValues(): void
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
