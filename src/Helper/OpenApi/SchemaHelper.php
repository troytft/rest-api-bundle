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
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => $nullable,
        ]);
    }

    public static function createInteger(bool $nullable): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::INTEGER,
            'nullable' => $nullable,
        ]);
    }

    public static function createFloat(bool $nullable): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::NUMBER,
            'format' => 'double',
            'nullable' => $nullable,
        ]);
    }

    public static function createBoolean(bool $nullable): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::BOOLEAN,
            'nullable' => $nullable,
        ]);
    }

    public static function createDate(string $format, bool $nullable = false): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'format' => 'date',
            'example' => static::createExampleDateTime()->format($format),
            'nullable' => $nullable,
        ]);
    }

    public static function createDateTime(string $format, bool $nullable = false): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'format' => 'date-time',
            'example' => static::createExampleDateTime()->format($format),
            'nullable' => $nullable,
        ]);
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
