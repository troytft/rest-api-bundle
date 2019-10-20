<?php

namespace Tests;

use Tests;
use RestApiBundle;

class ValidationTest extends BaseBundleTestCase
{
    public function testValidationException()
    {
        try {
            $model = new Tests\Mock\DemoBundle\RequestModel\ModelWithValidation();
            $this->getRequestModelManager()->handleRequest($model, [
                'stringField' => 's',
                'modelField' => [
                    'stringField' => 's',
                ],
                'collectionField' => [
                    [
                        'stringField' => 's',
                    ],
                ]
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
                '*' => [
                    'Example message without property',
                ],
            ];
            $this->assertSame($expected, $exception->getProperties());
        }
    }
}
