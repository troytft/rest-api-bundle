<?php

namespace RestApiBundle\Services\Docs\Schema;

use RestApiBundle;

abstract class BaseSchemaReader
{
    protected function createFromString(string $type, bool $nullable): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        switch ($type) {
            case 'string':
                $result = new RestApiBundle\DTO\Docs\Schema\StringType($nullable);

                break;

            case 'int':
            case 'integer':
                $result = new RestApiBundle\DTO\Docs\Schema\IntegerType($nullable);

                break;

            case 'float':
                $result = new RestApiBundle\DTO\Docs\Schema\FloatType($nullable);

                break;

            case 'bool':
            case 'boolean':
                $result = new RestApiBundle\DTO\Docs\Schema\BooleanType($nullable);

                break;

            case \DateTime::class:
                $result = new RestApiBundle\DTO\Docs\Schema\DateTimeType($nullable);

                break;

            default:
                $result = new RestApiBundle\DTO\Docs\Schema\ClassType($type, $nullable);
        }

        return $result;
    }
}
