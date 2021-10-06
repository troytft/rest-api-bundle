<?php

namespace RestApiBundle\Helper;

use RestApiBundle;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\PropertyInfo;
use phpDocumentor\Reflection as PhpDoc;

use function in_array;
use function var_dump;

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
            throw new RestApiBundle\Exception\Mapper\Schema\InvalidDefinitionException('Union types are not supported.');
        }

        return $result[0] ?? null;
    }

    public static function extractByPhpDocType(PhpDoc\Type $phpDocType): ?PropertyInfo\Type
    {
        $result = static::getPhpDocTypeHelper()->getTypes($phpDocType);

        if (count($result) > 1) {
            throw new RestApiBundle\Exception\Mapper\Schema\InvalidDefinitionException('Union types are not supported.');
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

    public static function extractPropertyType(\ReflectionProperty $reflectionProperty): ?PropertyInfo\Type
    {
        $result = null;
        $varTag = static::resolveVarTag($reflectionProperty);

        var_dump($reflectionProperty->getName(), $reflectionProperty->getType(), $reflectionProperty->getDeclaringClass());
        if ($varTag) {
            $result = RestApiBundle\Helper\TypeExtractor::extractByPhpDocType($varTag->getType());
        } elseif ($reflectionProperty->getType()) {
            $result = static::extractByReflectionType($reflectionProperty->getType());
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

    private static function resolveVarTag(\ReflectionProperty $reflectionProperty): ?PhpDoc\DocBlock\Tags\Var_
    {
        if (!$reflectionProperty->getDocComment()) {
            return null;
        }

        $docBlock = static::getDocBlockFactory()->create($reflectionProperty->getDocComment());
        $count = count($docBlock->getTagsByName('var'));

        if ($count === 0) {
            return null;
        }

        if ($count > 1) {
            throw new \LogicException();
        }

        $varTag = $docBlock->getTagsByName('var')[0];
        if (!$varTag instanceof PhpDoc\DocBlock\Tags\Var_ || !$varTag->getType()) {
            throw new \InvalidArgumentException();
        }

        return $varTag;
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
