<?php

class EnumTransformerTest extends Tests\BaseTestCase
{
    public function testSuccess()
    {
        $model = new Tests\Fixture\TestCases\RequestModel\EnumTransformer\Model();
        $this->getRequestModelHandler()->handle($model, [
            'value' => \Tests\Fixture\Common\Enum\BookStatus::CREATED,
        ]);

        $this->assertTrue($model->getValue() instanceof \Tests\Fixture\Common\Enum\BookStatus);
        $this->assertSame(\Tests\Fixture\Common\Enum\BookStatus::CREATED, $model->getValue()->getValue());
    }

    public function testValueNotFoundInEnum()
    {
        try {
            $model = new Tests\Fixture\TestCases\RequestModel\EnumTransformer\Model();
            $this->getRequestModelHandler()->handle($model, [
                'value' => 'invalid'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['value' => ['The value you selected is not a valid choice.']], $exception->getProperties());
        }
    }

    private function getRequestModelHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
