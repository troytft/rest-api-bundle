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
                    ],
                    null
                ]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            var_dump($exception->getProperties());
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
                'collectionField.1' => [
                    "This value should not be null."
                ],
                '*' => [
                    'Example message without property',
                ],
            ];
            $this->assertSame($expected, $exception->getProperties());
        }
    }
}
