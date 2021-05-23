<?php

namespace RestApiBundle\Helper;

use RestApiBundle;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\PropertyInfo;
use phpDocumentor\Reflection as PhpDoc;

use function in_array;

class TypeExtractor
{
    private static ?PropertyInfo\Util\PhpDocTypeHelper $phpDocTypeHelper = null;
    private static ?DocBlockFactory $docBlockFactory = null;

    public static function extractByReflectionType(\ReflectionType $sourceReflectionType): ?PropertyInfo\Type
    {
        $result = [];

        if ($sourceReflectionType instanceof \ReflectionUnionType) {
            $reflectionTypes = $sourceReflectionType->getTypes();
        } else {
            $reflectionTypes = [$sourceReflectionType];
        }

        foreach ($reflectionTypes as $reflectionType) {
            $phpTypeOrClass = $sourceReflectionType instanceof \ReflectionNamedType ? $sourceReflectionType->getName() : (string) $reflectionType;
            if ($phpTypeOrClass === PropertyInfo\Type::BUILTIN_TYPE_ARRAY || $phpTypeOrClass === 'mixed') {
                continue;
            }

            if ($phpTypeOrClass === 'null' | $phpTypeOrClass === 'void') {
                $result[] = new PropertyInfo\Type(PropertyInfo\Type::BUILTIN_TYPE_NULL, $sourceReflectionType->allowsNull());
            } elseif ($reflectionType->isBuiltin()) {
                $result[] = new PropertyInfo\Type($phpTypeOrClass, $sourceReflectionType->allowsNull());
            } else {
                $result[] = new PropertyInfo\Type(PropertyInfo\Type::BUILTIN_TYPE_OBJECT, $sourceReflectionType->allowsNull(), $phpTypeOrClass);
            }
        }

        if (count($result) > 1) {
            throw new \InvalidArgumentException('Union types not supported yet');
        }

        return $result[0] ?? null;
    }

    public static function extractByPhpDocType(PhpDoc\Type $phpDocType): ?PropertyInfo\Type
    {
        $result = static::getPhpDocTypeHelper()->getTypes($phpDocType);

        if (count($result) > 1) {
            throw new \InvalidArgumentException('Union types not supported yet');
        }

        return $result[0] ?? null;
    }

    private static function getPhpDocTypeHelper(): PropertyInfo\Util\PhpDocTypeHelper
    {
        if (!static::$phpDocTypeHelper) {
            static::$phpDocTypeHelper = new PropertyInfo\Util\PhpDocTypeHelper();
        }

        return static::$phpDocTypeHelper;
    }

    public static function extractReturnType(\ReflectionMethod $reflectionMethod): ?PropertyInfo\Type
    {
        $result = null;
        $returnTag = static::resolveReturnTag($reflectionMethod);

        if ($returnTag) {
            $result = RestApiBundle\Helper\TypeExtractor::extractByPhpDocType($returnTag->getType());
        } elseif ($reflectionMethod->getReturnType()) {
            $result = static::extractByReflectionType($reflectionMethod->getReturnType());
        }

        return $result;
    }

    private static function resolveReturnTag(\ReflectionMethod $reflectionMethod): ?Return_
    {
        if (!$reflectionMethod->getDocComment()) {
            return null;
        }

        $docBlock = static::getDocBlockFactory()->create($reflectionMethod->getDocComment());
        $count = count($docBlock->getTagsByName('return'));

        if ($count === 0) {
            return null;
        }

        if ($count > 1) {
            throw new RestApiBundle\Exception\OpenApi\InvalidDefinition\TwoOrMoreReturnTagsException();
        }

        $returnTag = $docBlock->getTagsByName('return')[0];
        if (!$returnTag instanceof Return_ || !$returnTag->getType()) {
            throw new \InvalidArgumentException();
        }

        return $returnTag;
    }

    private static function getDocBlockFactory(): DocBlockFactory
    {
        if (!static::$docBlockFactory) {
            static::$docBlockFactory = DocBlockFactory::createInstance();
        }

        return static::$docBlockFactory;
    }

    public static function isScalar(PropertyInfo\Type $type): bool
    {
        $types = [
            PropertyInfo\Type::BUILTIN_TYPE_INT,
            PropertyInfo\Type::BUILTIN_TYPE_BOOL,
            PropertyInfo\Type::BUILTIN_TYPE_STRING,
            PropertyInfo\Type::BUILTIN_TYPE_FLOAT,
        ];

        return in_array($type->getBuiltinType(), $types, true);
    }
}
