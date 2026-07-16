<?php

class EnumTransformerTest extends Tests\BaseTestCase
{
    public function testSuccess(): void
    {
        $model = new Tests\Fixture\Mapper\EnumTransformerTest\Model();
        $this->getMapper()->map($model, [
            'field' => \Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::CREATED,
        ]);

        $this->assertTrue($model->getField() instanceof \Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString);
        $this->assertSame(\Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::CREATED, $model->getField()->getValue());
    }

    public function testValueNotFound(): void
    {
        try {
            $model = new Tests\Fixture\Mapper\EnumTransformerTest\Model();
            $this->getMapper()->map($model, [
                'field' => 'invalid'
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['field' => ['The value you selected is not a valid choice.']], $exception->getProperties());
        }
    }

    public function testPhpIntegerFromInteger(): void
    {
        $model = new Tests\Fixture\Mapper\EnumTransformerTest\PhpIntegerModel();
        $this->getMapper()->map($model, [
            'field' => 1,
        ]);

        $this->assertSame(\Tests\Fixture\TestApp\Enum\NamespaceExample\PhpInteger::PUBLISHED, $model->getField());
    }

    public function testPhpIntegerFromNumericString(): void
    {
        $model = new Tests\Fixture\Mapper\EnumTransformerTest\PhpIntegerModel();
        $this->getMapper()->map($model, [
            'field' => '1',
        ]);

        $this->assertSame(\Tests\Fixture\TestApp\Enum\NamespaceExample\PhpInteger::PUBLISHED, $model->getField());
    }

    public function testPhpIntegerFromNotNumericString(): void
    {
        try {
            $model = new Tests\Fixture\Mapper\EnumTransformerTest\PhpIntegerModel();
            $this->getMapper()->map($model, [
                'field' => 'invalid',
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['field' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testPhpStringFromString(): void
    {
        $model = new Tests\Fixture\Mapper\EnumTransformerTest\PhpStringModel();
        $this->getMapper()->map($model, [
            'field' => 'published',
        ]);

        $this->assertSame(\Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::PUBLISHED, $model->getField());
    }

    public function testPhpStringFromInteger(): void
    {
        try {
            $model = new Tests\Fixture\Mapper\EnumTransformerTest\PhpStringModel();
            $this->getMapper()->map($model, [
                'field' => 1,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['field' => ['The value you selected is not a valid choice.']], $exception->getProperties());
        }
    }

    public function testPhpEnumFromArray(): void
    {
        try {
            $model = new Tests\Fixture\Mapper\EnumTransformerTest\PhpIntegerModel();
            $this->getMapper()->map($model, [
                'field' => [],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['field' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testPurePhpEnumIsNotSupported(): void
    {
        try {
            $model = new Tests\Fixture\Mapper\EnumTransformerTest\PhpPureModel();
            $this->getMapper()->map($model, [
                'field' => 'CREATED',
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\ContextAware\PropertyAwareException $exception) {
            $this->assertSame(
                'Enum "Tests\Fixture\TestApp\Enum\NamespaceExample\PhpPure" must be backed by int or string. Tests\Fixture\Mapper\EnumTransformerTest\PhpPureModel::$field',
                $exception->getMessage()
            );
        }
    }
}
