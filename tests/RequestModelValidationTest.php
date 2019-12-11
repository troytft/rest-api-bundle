<?php

namespace Tests;

use Tests;
use RestApiBundle;

class RequestModelValidationTest extends BaseBundleTestCase
{
    public function testValidationException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithValidation();
            $this->getRequestModelManager()->handle($model, [
                'stringField' => 's',
                'modelField' => [
                    'stringField' => 's',
                ],
                'collectionField' => [
                    [
                        'stringField' => 's',
                    ]
                ],
                'collectionOfIntegers' => [1, 12, 5],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $expected = [
                'stringField' => [
                    'This value is too short. It should have 6 characters or more.',
                    'This value is not a valid email address.',
                ],
                'modelField.stringField' => [
                    'This value is too short. It should have 3 characters or more.',
                ],
                'collectionField.0.stringField' => [
                    'This value is too short. It should have 3 characters or more.',
                ],
                'collectionOfIntegers.0' => [
                    'This value should be 10 or more.'
                ],
                'collectionOfIntegers.2' => [
                    'This value should be 10 or more.'
                ],
                '*' => [
                    'Example message without property'
                ]
            ];
            $this->assertSame($expected, $exception->getProperties());
        }
    }
}
