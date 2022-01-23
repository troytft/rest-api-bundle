<?php

class EnumTransformerTest extends Tests\BaseTestCase
{
    public function testSuccess()
    {
        $model = new Tests\Fixture\RequestModel\EnumTransformerTest\Model();
        $this->getRequestModelHandler()->handle($model, [
            'field' => Tests\Fixture\TestApp\Enum\BookStatus::CREATED,
        ]);

        $this->assertTrue($model->getField() instanceof Tests\Fixture\TestApp\Enum\BookStatus);
        $this->assertSame(\Tests\Fixture\TestApp\Enum\BookStatus::CREATED, $model->getField()->getValue());
    }

    public function testValueNotFound()
    {
        try {
            $model = new Tests\Fixture\RequestModel\EnumTransformerTest\Model();
            $this->getRequestModelHandler()->handle($model, [
                'field' => 'invalid'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['field' => ['The value you selected is not a valid choice.']], $exception->getProperties());
        }
    }

    private function getRequestModelHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
