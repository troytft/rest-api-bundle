<?php

declare(strict_types=1);

namespace RestApiBundle\Helper\OpenApi;

use cebe\openapi\spec as OpenApi;
use RestApiBundle;
use Symfony\Component\PropertyInfo;

final class SchemaHelper
{
    private static function createExampleDateTime(): \DateTime
    {
        $result = new \DateTime();
        $result
            ->setTimestamp(1617885866)
            ->setTimezone(new \DateTimeZone('Europe/Prague'));

        return $result;
    }

    public static function createString(bool $nullable): OpenApi\Schema
    {
        $schema = new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
        ]);

        if ($nullable) {
            $schema->nullable = true;
        }

        return $schema;
    }

    public static function createInteger(bool $nullable): OpenApi\Schema
    {
        $schema = new OpenApi\Schema([
            'type' => OpenApi\Type::INTEGER,
            'format' => 'int64',
        ]);

        if ($nullable) {
            $schema->nullable = true;
        }

        return $schema;
    }

    public static function createFloat(bool $nullable): OpenApi\Schema
    {
        $schema = new OpenApi\Schema([
            'type' => OpenApi\Type::NUMBER,
            'format' => 'double',
        ]);

        if ($nullable) {
            $schema->nullable = true;
        }

        return $schema;
    }

    public static function createBoolean(bool $nullable): OpenApi\Schema
    {
        $schema = new OpenApi\Schema([
            'type' => OpenApi\Type::BOOLEAN,
        ]);

        if ($nullable) {
            $schema->nullable = true;
        }

        return $schema;
    }

    public static function createDate(string $format, bool $nullable = false): OpenApi\Schema
    {
        $schema = new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'format' => 'date',
            'example' => static::createExampleDateTime()->format($format),
        ]);

        if ($nullable) {
            $schema->nullable = true;
        }

        return $schema;
    }

    public static function createDateTime(string $format, bool $nullable = false): OpenApi\Schema
    {
        $schema = new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'format' => 'date-time',
            'example' => static::createExampleDateTime()->format($format),
        ]);

        if ($nullable) {
            $schema->nullable = true;
        }

        return $schema;
    }

    public static function createEnum(string $class, bool $nullable = false): OpenApi\Schema
    {
        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData($class);
        if ($enumData->type === PropertyInfo\Type::BUILTIN_TYPE_STRING) {
            $schema = static::createString($nullable);
        } elseif ($enumData->type === PropertyInfo\Type::BUILTIN_TYPE_INT) {
            $schema = static::createInteger($nullable);
        } else {
            throw new \LogicException();
        }

        $schema->enum = $enumData->values;

        return $schema;
    }
}
