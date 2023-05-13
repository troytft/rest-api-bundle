<?php

namespace RestApiBundle\Helper;

use RestApiBundle;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\PropertyInfo;
use phpDocumentor\Reflection as PhpDoc;

use function in_array;

final class TypeExtractor
{
    private static ?PropertyInfo\Util\PhpDocTypeHelper $phpDocTypeHelper = null;
    private static ?DocBlockFactory $docBlockFactory = null;

    private static function extractByReflectionType(\ReflectionType $sourceReflectionType): ?PropertyInfo\Type
    {
        $result = [];

        if ($sourceReflectionType instanceof \ReflectionUnionType) {
            $reflectionTypes = $sourceReflectionType->getTypes();
        } else {
            $reflectionTypes = [$sourceReflectionType];
        }

        foreach ($reflectionTypes as $reflectionType) {
            $phpTypeOrClass = $sourceReflectionType instanceof \ReflectionNamedType ? $sourceReflectionType->getName() : (string) $reflectionType;
            if ($phpTypeOrClass === 'mixed') {
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

    private static function extractByPhpDoc(PhpDoc\Type $phpDocType): ?PropertyInfo\Type
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

    public static function extractByReflectionParameter(\ReflectionParameter $reflectionParameter): ?PropertyInfo\Type
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        $result = static::extractByReflectionType($reflectionParameter->getType());

        return $result?->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_ARRAY ? null : $result;
    }

    public static function extractByReflectionMethod(\ReflectionMethod $reflectionMethod): ?PropertyInfo\Type
    {
        $returnTag = static::resolveReturnTag($reflectionMethod);
        $typeByPhpDoc = $returnTag ? RestApiBundle\Helper\TypeExtractor::extractByPhpDoc($returnTag->getType()) : null;
        $typeByReflection = $reflectionMethod->getReturnType() ? static::extractByReflectionType($reflectionMethod->getReturnType()) : null;

        if ($typeByPhpDoc && $typeByReflection) {
            if ($typeByPhpDoc->isNullable() !== $typeByReflection->isNullable() || $typeByPhpDoc->getBuiltinType() !== $typeByReflection->getBuiltinType()) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('DocBlock type and code type mismatch', $reflectionMethod);
            }
        }

        if ($typeByReflection?->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_ARRAY) {
            $typeByReflection = null;
        }

        return $typeByPhpDoc ?: $typeByReflection;
    }

    public static function extractByReflectionProperty(\ReflectionProperty $reflectionProperty): ?PropertyInfo\Type
    {
        $varTag = static::resolveVarTag($reflectionProperty);
        $typeByPhpDoc = $varTag ? RestApiBundle\Helper\TypeExtractor::extractByPhpDoc($varTag->getType()) : null;
        $typeByReflection = $reflectionProperty->getType() ? static::extractByReflectionType($reflectionProperty->getType()) : null;

        if ($typeByPhpDoc && $typeByReflection) {
            if ($typeByPhpDoc->isNullable() !== $typeByReflection->isNullable() || $typeByPhpDoc->getBuiltinType() !== $typeByReflection->getBuiltinType()) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionPropertyAwareException('DocBlock type and code type mismatch', $reflectionProperty);
            }
        }

        if ($typeByReflection?->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_ARRAY) {
            $typeByReflection = null;
        }

        return $typeByPhpDoc ?: $typeByReflection;
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
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('DocBlock contains two or more return tags.', $reflectionMethod);
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

    public static function extractFirstCollectionValueType(PropertyInfo\Type $type): ?PropertyInfo\Type
    {
        if (count($type->getCollectionValueTypes()) > 1) {
            throw new \InvalidArgumentException();
        }

        return $type->getCollectionValueTypes()[0] ?? null;
    }

    public static function extractEnumData(string $class): RestApiBundle\Model\Helper\TypeExtractor\EnumData
    {
        if (method_exists($class, 'getValues')) {
            $values = $class::getValues();
        } else {
            $reflectionClass = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class);

            $values = [];
            foreach ($reflectionClass->getReflectionConstants(\ReflectionClassConstant::IS_PUBLIC) as $reflectionConstant) {
                if (is_scalar($reflectionConstant->getValue())) {
                    $values[] = $reflectionConstant->getValue();
                }
            }
        }

        if (!$values) {
            throw new \LogicException();
        }

        $types = [];
        foreach ($values as $value) {
            if (is_int($value)) {
                $types[PropertyInfo\Type::BUILTIN_TYPE_INT] = true;
            } elseif (is_string($value)) {
                $types[PropertyInfo\Type::BUILTIN_TYPE_STRING] = true;
            } elseif (is_float($value)) {
                $types[PropertyInfo\Type::BUILTIN_TYPE_FLOAT] = true;
            } else {
                throw new \InvalidArgumentException();
            }
        }

        $types = array_keys($types);
        if (count($types) === 1) {
            $type = $types[0];
        } else {
            if (in_array(PropertyInfo\Type::BUILTIN_TYPE_STRING, $types, true)) {
                $type = PropertyInfo\Type::BUILTIN_TYPE_STRING;
            } elseif (in_array(PropertyInfo\Type::BUILTIN_TYPE_FLOAT, $types, true)) {
                $type = PropertyInfo\Type::BUILTIN_TYPE_FLOAT;
            } else {
                $type = PropertyInfo\Type::BUILTIN_TYPE_INT;
            }
        }

        return new RestApiBundle\Model\Helper\TypeExtractor\EnumData($type, $values);
    }
}
