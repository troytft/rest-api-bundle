<?php

namespace Tests\Benchmark;

use Tests;
use RestApiBundle;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

class MapperBench
{
    /**
     * @Revs(100)
     */
    public function benchComplexModelSchemaResolving(): void
    {
        $schemaResolver = new RestApiBundle\Services\Mapper\SchemaResolver();
        $schemaResolver
            ->resolve(Tests\Fixture\Mapper\Benchmark\RootModel::class);
    }

    /**
     * @Revs(100)
     */
    public function benchComplexModelMapping(): void
    {
        $model = new Tests\Fixture\Mapper\Benchmark\RootModel();
        $data = [
            'group1' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group2' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group3' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group4' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group5' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group6' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group7' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group8' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group9' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
            'group10' => [
                'model1' => [
                    'field1' => 'value',
                ],
                'model2' => [
                    'field1' => 'value',
                ],
                'model3' => [
                    'field1' => 'value',
                ],
                'model4' => [
                    'field1' => 'value',
                ],
                'model5' => [
                    'field1' => 'value',
                ],
                'model6' => [
                    'field1' => 'value',
                ],
                'model7' => [
                    'field1' => 'value',
                ],
                'model8' => [
                    'field1' => 'value',
                ],
                'model9' => [
                    'field1' => 'value',
                ],
                'model10' => [
                    'field1' => 'value',
                ],
            ],
        ];

        $schemaResolver = new RestApiBundle\Services\Mapper\SchemaResolver();
        $mapper = new RestApiBundle\Services\Mapper\Mapper($schemaResolver);
        $mapper
            ->addTransformer(new RestApiBundle\Services\Mapper\Transformer\IntegerTransformer())
            ->addTransformer(new RestApiBundle\Services\Mapper\Transformer\StringTransformer())
            ->addTransformer(new RestApiBundle\Services\Mapper\Transformer\FloatTransformer())
            ->addTransformer(new RestApiBundle\Services\Mapper\Transformer\BooleanTransformer());

        $mapper->map($model, $data);
    }
}
