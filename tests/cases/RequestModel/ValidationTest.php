<?php

declare(strict_types=1);

class ValidationTest extends Tests\BaseTestCase
{
    public function testNestedValidation()
    {
        $model = new Tests\Fixture\Mapper\ValidationTest\TestNestedValidationModel();

        // nested model
        try {
            $this->getMapper()->map($model, [
                'childModel' => [],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'childModel.field' => ['This value is not valid.'],
            ], $exception->getProperties());
        }

        // nested collection of models
        try {
            $this->getMapper()->map($model, [
                'childModels' => [
                    [
                    ],
                    [
                    ],
                ],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'childModels.0.field' => ['This value is not valid.'],
                'childModels.1.field' => ['This value is not valid.'],
            ], $exception->getProperties());
        }
    }

    public function testClearMissing()
    {
        // enabled
        $model = new Tests\Fixture\Mapper\ValidationTest\TestClearMissingModel();
        try {
            $this->getMapper()->map($model, [], new RestApiBundle\Model\Mapper\Context(clearMissing: true));
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'field' => ['This value should not be null.'],
            ], $exception->getProperties());
        }

        // disabled
        $model = new Tests\Fixture\Mapper\ValidationTest\TestClearMissingModel();
        $this->assertSame('default value', $model->field);

        $this->getMapper()->map($model, [], new RestApiBundle\Model\Mapper\Context(clearMissing: false));
        $this->assertSame('default value', $model->field);
    }

    public function testUndefinedKey()
    {
        $model = new Tests\Fixture\Mapper\ValidationTest\TestUndefinedKeyModel();

        try {
            $this->getMapper()->map($model, [
                'keyNotDefinedInModel' => null,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['keyNotDefinedInModel' => ['The key is not defined in the model.']], $exception->getProperties());
        }
    }
}
