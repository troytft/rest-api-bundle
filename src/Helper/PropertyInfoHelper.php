<?php

namespace RestApiBundle\Helper;

use Symfony\Component\PropertyInfo;

class PropertyInfoHelper
{
    public static function format(PropertyInfo\Type $propertyInfoType): string
    {
        $prefix = $propertyInfoType->isNullable() ? '?' : '';
        $builtinType = $propertyInfoType->getBuiltinType();

        if ($builtinType === PropertyInfo\Type::BUILTIN_TYPE_OBJECT) {
            $className = $propertyInfoType->getClassName();
            if ($className !== null) {
                return $prefix . $className;
            }

            return $prefix . 'object';
        }

        if ($builtinType === PropertyInfo\Type::BUILTIN_TYPE_ARRAY) {
            $collectionKeyType = $propertyInfoType->getCollectionKeyTypes();
            $collectionValueType = $propertyInfoType->getCollectionValueTypes();

            $keyType = $collectionKeyType ? static::format($collectionKeyType[0]) : 'mixed';
            $valueType = $collectionValueType ? static::format($collectionValueType[0]) : 'mixed';

            return $prefix . "array<{$keyType}, {$valueType}>";
        }

        return $prefix . $builtinType;
    }
}
