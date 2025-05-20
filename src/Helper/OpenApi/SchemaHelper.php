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

    public static function createScalarFromPropertyInfoType(PropertyInfo\Type $type): OpenApi\Schema
    {
        return static::createScalarFromString($type->getBuiltinType(), $type->isNullable());
    }

    public static function createScalarFromString(string $type, bool $nullable = false): OpenApi\Schema
    {
        return match ($type) {
            PropertyInfo\Type::BUILTIN_TYPE_STRING => new OpenApi\Schema([
                'type' => OpenApi\Type::STRING,
                'nullable' => $nullable,
            ]),
            PropertyInfo\Type::BUILTIN_TYPE_INT => new OpenApi\Schema([
                'type' => OpenApi\Type::INTEGER,
                'nullable' => $nullable,
            ]),
            PropertyInfo\Type::BUILTIN_TYPE_FLOAT => new OpenApi\Schema([
                'type' => OpenApi\Type::NUMBER,
                'format' => 'double',
                'nullable' => $nullable,
            ]),
            PropertyInfo\Type::BUILTIN_TYPE_BOOL => new OpenApi\Schema([
                'type' => OpenApi\Type::BOOLEAN,
                'nullable' => $nullable,
            ]),
            default => throw new \InvalidArgumentException(),
        };
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

        $allowedTypes = [
            PropertyInfo\Type::BUILTIN_TYPE_STRING,
            PropertyInfo\Type::BUILTIN_TYPE_INT,
            PropertyInfo\Type::BUILTIN_TYPE_FLOAT,
        ];
        if (!in_array($enumData->type, $allowedTypes, true)) {
            throw new \LogicException();
        }

        $result = static::createScalarFromString($enumData->type, $nullable);
        $result->enum = $enumData->values;

        return $result;
    }
}
