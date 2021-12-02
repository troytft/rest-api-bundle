<?php

class TransformersTest extends Tests\BaseTestCase
{
    public function testBooleanTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\BooleanTransformer();

        $this->assertSame(true, $transformer->transform(true));
        $this->assertSame(true, $transformer->transform('true'));
        $this->assertSame(false, $transformer->transform(false));
        $this->assertSame(false, $transformer->transform('false'));
    }

    public function testIntegerTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\IntegerTransformer();

        $this->assertSame(10, $transformer->transform(10));
        $this->assertSame(10, $transformer->transform('10'));
        $this->assertSame(10, $transformer->transform(10.0));

        try {
            $transformer->transform(10.1);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
        }
    }

    public function testFloatTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\FloatTransformer();

        $this->assertSame(10.0, $transformer->transform(10.0));
        $this->assertSame(10.0, $transformer->transform(10));
        $this->assertSame(10.0, $transformer->transform('10'));
        $this->assertSame(10.1, $transformer->transform('10.1'));

        try {
            $transformer->transform('s');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException $exception) {
        }
    }

    public function testStringTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\StringTransformer();

        $this->assertSame('10', $transformer->transform(10));
        $this->assertSame('10', $transformer->transform('10'));
        $this->assertSame('10', $transformer->transform(10.0));
    }

    public function testTimestampTransformer()
    {
        $transformer = new RestApiBundle\Services\Mapper\Transformer\TimestampTransformer();

        $this->assertTrue($transformer->transform(1620000000) instanceof \DateTime);

        try {
            $transformer->transform('s');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
        }
    }

    public function testDateTimeFormatOption()
    {
        $options = [
            RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION => 'Y/m/d-H:i:sP'
        ];

        // invalid date format
        try {
            $this->getDateTimeTransformer()->transform('2021-10-01T16:00:00+00:00', $options);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\InvalidDateTimeFormatException $exception) {
            $this->assertSame('Y/m/d-H:i:sP', $exception->getFormat());
        }

        // success
        $value = $this->getDateTimeTransformer()->transform('2021/10/01-16:00:00+00:00', $options);
        $this->assertInstanceOf(\DateTime::class, $value);
    }

    public function testDateFormatOption()
    {
        $options = [
            RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION => 'Y/m/d'
        ];

        // invalid date format
        try {
            $this->getDateTransformer()->transform('2021-10-010', $options);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\InvalidDateFormatException $exception) {
            $this->assertSame('Y/m/d', $exception->getFormat());
        }

        // success
        $value = $this->getDateTransformer()->transform('2021/10/01', $options);
        $this->assertInstanceOf(\DateTime::class, $value);
    }

    public function testDateTimeForceLocalTimezoneOption()
    {
        $datetime = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Prague');
        $datetime->setTimezone($timezone);

        // false
        $options = [
            RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORCE_LOCAL_TIMEZONE_OPTION => false
        ];

        $value = $this->getDateTimeTransformer()->transform('2021-10-01T16:00:00+03:00', $options);
        $this->assertSame('+03:00', $value->getTimezone()->getName());

        // true
        $options = [
            RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORCE_LOCAL_TIMEZONE_OPTION => true
        ];

        $value = $this->getDateTimeTransformer()->transform('2021-10-01T16:00:00+03:00', $options);
        $this->assertSame('UTC', $value->getTimezone()->getName());
    }

    public function testInvalidDateException()
    {
        try {
            $this->getDateTransformer()->transform('2021-10-33');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\InvalidDateException $exception) {
            $this->assertSame('The parsed date was invalid', $exception->getErrorMessage());
        }
    }

    public function testInvalidDateTimeException()
    {
        try {
            $this->getDateTimeTransformer()->transform('2021-10-33T16:00:00+03:00');
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\Transformer\InvalidDateTimeException $exception) {
            $this->assertSame('The parsed date was invalid', $exception->getErrorMessage());
        }
    }

    private function getDateTimeTransformer(): RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class);
    }

    private function getDateTransformer(): RestApiBundle\Services\Mapper\Transformer\DateTransformer
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\Transformer\DateTransformer::class);
    }
}
