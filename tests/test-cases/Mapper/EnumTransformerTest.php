<?php

class EnumTransformerTest extends Tests\BaseTestCase
{
    public function testSuccess()
    {
        $model = new Tests\Fixture\Mapper\EnumTransformerTest\Model();
        $this->getMapper()->map($model, [
            'field' => Tests\Fixture\TestApp\Enum\BookStatus::CREATED,
        ]);

        $this->assertTrue($model->getField() instanceof Tests\Fixture\TestApp\Enum\BookStatus);
        $this->assertSame(\Tests\Fixture\TestApp\Enum\BookStatus::CREATED, $model->getField()->getValue());
    }

    public function testValueNotFound()
    {
        try {
            $model = new Tests\Fixture\Mapper\EnumTransformerTest\Model();
            $this->getMapper()->map($model, [
                'field' => 'invalid'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['field' => ['The value you selected is not a valid choice.']], $exception->getErrors());
        }
    }
}
