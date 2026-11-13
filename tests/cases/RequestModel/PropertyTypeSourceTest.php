<?php

class PropertyTypeSourceTest extends Tests\BaseTestCase
{
    public function testGetterReturnDocBlockIsIgnored(): void
    {
        $this->assertTypeIsTakenFromProperty(new Tests\Fixture\Mapper\PropertyTypeSourceTest\GetterReturnModel());
    }

    public function testSetterParamDocBlockIsIgnored(): void
    {
        $this->assertTypeIsTakenFromProperty(new Tests\Fixture\Mapper\PropertyTypeSourceTest\SetterParamModel());
    }

    public function testConstructorParamIsIgnored(): void
    {
        $this->assertTypeIsTakenFromProperty(new Tests\Fixture\Mapper\PropertyTypeSourceTest\ConstructorParamModel());
    }

    public function testAdderParamIsIgnored(): void
    {
        $this->assertTypeIsTakenFromProperty(new Tests\Fixture\Mapper\PropertyTypeSourceTest\AdderModel());
    }

    private function assertTypeIsTakenFromProperty(
        Tests\Fixture\Mapper\PropertyTypeSourceTest\GetterReturnModel|Tests\Fixture\Mapper\PropertyTypeSourceTest\SetterParamModel|Tests\Fixture\Mapper\PropertyTypeSourceTest\ConstructorParamModel|Tests\Fixture\Mapper\PropertyTypeSourceTest\AdderModel $model,
    ): void
    {
        $this->getMapper()->map($model, ['field' => 'value']);
        $this->assertSame('value', $model->field);

        $this->getMapper()->map($model, ['field' => null]);
        $this->assertNull($model->field);
    }
}
