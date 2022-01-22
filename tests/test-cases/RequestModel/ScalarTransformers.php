<?php

class ScalarTransformers extends Tests\BaseTestCase
{
    public function testBooleanTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\BooleanTransformer();

        // positive scenarios
        $this->assertSame(true, $transformer->transform(true));
        $this->assertSame(true, $transformer->transform('true'));
        $this->assertSame(false, $transformer->transform(false));
        $this->assertSame(false, $transformer->transform('false'));
        $this->assertSame(true, $transformer->transform('1'));
        $this->assertSame(false, $transformer->transform('0'));
        $this->assertSame(false, $transformer->transform(''));
        $this->assertSame(true, $transformer->transform(1));
        $this->assertSame(false, $transformer->transform(0));

        // negative scenarios
        try {
            $transformer->transform('10');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\BooleanRequiredException $exception) {
        }

        try {
            $transformer->transform(10);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\BooleanRequiredException $exception) {
        }

        try {
            $transformer->transform(null);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\BooleanRequiredException $exception) {
        }
    }

    public function testIntegerTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\IntegerTransformer();

        // positive scenarios
        $this->assertSame(10, $transformer->transform(10));
        $this->assertSame(10, $transformer->transform(10.0));
        $this->assertSame(10, $transformer->transform('10'));

        // negative scenarios
        try {
            $transformer->transform(10.1);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
        }

        try {
            $transformer->transform('10.1');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
        }

        try {
            $transformer->transform(true);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
        }

        try {
            $transformer->transform(null);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
        }

        try {
            $transformer->transform('');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
        }
    }

    public function testFloatTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\FloatTransformer();

        // positive scenarios
        $this->assertSame(10.0, $transformer->transform(10.0));
        $this->assertSame(10.0, $transformer->transform(10));
        $this->assertSame(10.0, $transformer->transform('10'));
        $this->assertSame(10.1, $transformer->transform('10.1'));

        // negative scenarios
        try {
            $transformer->transform('');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException $exception) {
        }

        try {
            $transformer->transform('s');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException $exception) {
        }

        try {
            $transformer->transform(true);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException $exception) {
        }

        try {
            $transformer->transform(null);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException $exception) {
        }
    }

    public function testStringTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\StringTransformer();

        // positive scenarios
        $this->assertSame('10', $transformer->transform(10));
        $this->assertSame('10', $transformer->transform('10'));
        $this->assertSame('10', $transformer->transform(10.0));
        $this->assertSame('', $transformer->transform(''));

        // negative scenarios
        try {
            $transformer->transform(true);
        } catch (RestApiBundle\Exception\Mapper\Transformer\StringRequiredException $exception) {
        }

        try {
            $transformer->transform(null);
        } catch (RestApiBundle\Exception\Mapper\Transformer\StringRequiredException $exception) {
        }
    }
}
