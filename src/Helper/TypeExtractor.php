<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

use phpDocumentor\Reflection as PhpDoc;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use RestApiBundle;
use Symfony\Component\PropertyInfo;

final class TypeExtractor
{
    private static ?PropertyInfo\Util\PhpDocTypeHelper $docBlockHelper = null;
    private static ?PhpDoc\DocBlockFactoryInterface $docBlockFactory = null;

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
            } elseif ($reflectionType instanceof \ReflectionNamedType && $reflectionType->isBuiltin()) {
                $result[] = new PropertyInfo\Type($phpTypeOrClass, $sourceReflectionType->allowsNull());
            } else {
                $result[] = new PropertyInfo\Type(PropertyInfo\Type::BUILTIN_TYPE_OBJECT, $sourceReflectionType->allowsNull(), $phpTypeOrClass);
            }
        }

        if (\count($result) > 1) {
            throw new RestApiBundle\Exception\Schema\InvalidDefinitionException('Union types are not supported.');
        }

        return $result[0] ?? null;
    }

    private static function extractByDocBlockTag(PhpDoc\Type $phpDocType): ?PropertyInfo\Type
    {
        $result = static::getDocBlockHelper()->getTypes($phpDocType);
        if (\count($result) > 1) {
            throw new RestApiBundle\Exception\Schema\InvalidDefinitionException('Union types are not supported.');
        }

        return $result[0] ?? null;
    }

    private static function getDocBlockHelper(): PropertyInfo\Util\PhpDocTypeHelper
    {
        if (!static::$docBlockHelper) {
            static::$docBlockHelper = new PropertyInfo\Util\PhpDocTypeHelper();
        }

        return static::$docBlockHelper;
    }

    private static function extract(?\ReflectionType $reflectionType, ?PhpDoc\Type $docBlockType): ?PropertyInfo\Type
    {
        $typeByDocBlock = $docBlockType ? static::extractByDocBlockTag($docBlockType) : null;
        $typeByReflection = $reflectionType ? static::extractByReflectionType($reflectionType) : null;

        if ($typeByDocBlock && $typeByReflection) {
            if ($typeByDocBlock->isNullable() !== $typeByReflection->isNullable() || $typeByDocBlock->getBuiltinType() !== $typeByReflection->getBuiltinType()) {
                throw new RestApiBundle\Exception\Schema\InvalidDefinitionException('DocBlock type and code type mismatch');
            }
        }

        if ($typeByReflection?->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_ARRAY) {
            $typeByReflection = null;
        }

        return $typeByDocBlock ?: $typeByReflection;
    }

    public static function extractByReflectionParameter(\ReflectionParameter $reflectionParameter): ?PropertyInfo\Type
    {
        return static::extract($reflectionParameter->getType(), null);
    }

    public static function extractByReflectionMethod(\ReflectionMethod $reflectionMethod): ?PropertyInfo\Type
    {
        try {
            $returnTag = static::resolveReturnTag($reflectionMethod);
            $result = static::extract($reflectionMethod->getReturnType(), $returnTag?->getType());
        } catch (RestApiBundle\Exception\Schema\InvalidDefinitionException $exception) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException($exception->getMessage(), $reflectionMethod);
        }

        return $result;
    }

    private static function resolveReturnTag(\ReflectionMethod $reflectionMethod): ?Return_
    {
        if (!$reflectionMethod->getDocComment()) {
            return null;
        }

        $docBlock = static::getDocBlockFactory()->create($reflectionMethod->getDocComment());
        $count = \count($docBlock->getTagsByName('return'));

        if ($count === 0) {
            return null;
        }

        if ($count > 1) {
            throw new RestApiBundle\Exception\Schema\InvalidDefinitionException('DocBlock contains two or more return tags.');
        }

        $returnTag = $docBlock->getTagsByName('return')[0];
        if (!$returnTag instanceof Return_ || !$returnTag->getType()) {
            throw new \InvalidArgumentException();
        }

        return $returnTag;
    }

    private static function getDocBlockFactory(): PhpDoc\DocBlockFactoryInterface
    {
        if (!static::$docBlockFactory) {
            static::$docBlockFactory = DocBlockFactory::createInstance();
        }

        return static::$docBlockFactory;
    }

    public static function extractCollectionValueType(PropertyInfo\Type $type): PropertyInfo\Type
    {
        if (\count($type->getCollectionValueTypes()) > 1) {
            throw new \InvalidArgumentException();
        }

        $result = $type->getCollectionValueTypes()[0] ?? null;
        if (!$result) {
            throw new \LogicException('Collection value type is empty');
        }

        return $result;
    }

    public static function extractEnumData(string $class): RestApiBundle\Model\Helper\TypeExtractor\EnumData
    {
        $values = null;

        if (class_exists($class) && enum_exists($class)) {
            foreach ($class::cases() as $case) {
                if ($case instanceof \BackedEnum) {
                    $values[] = $case->value;
                } else {
                    throw new \InvalidArgumentException();
                }
            }
        } elseif (method_exists($class, 'getValues')) {
            $values = $class::getValues();
        } else {
            $reflectionClass = ReflectionHelper::getReflectionClass($class);
            foreach ($reflectionClass->getReflectionConstants(\ReflectionClassConstant::IS_PUBLIC) as $reflectionConstant) {
                if (\is_scalar($reflectionConstant->getValue())) {
                    $values[] = $reflectionConstant->getValue();
                }
            }
        }

        if (!$values) {
            throw new \LogicException();
        }

        $types = [];
        foreach ($values as $value) {
            if (\is_int($value)) {
                $types[PropertyInfo\Type::BUILTIN_TYPE_INT] = true;
            } elseif (\is_string($value)) {
                $types[PropertyInfo\Type::BUILTIN_TYPE_STRING] = true;
            } elseif (\is_float($value)) {
                $types[PropertyInfo\Type::BUILTIN_TYPE_FLOAT] = true;
            } else {
                throw new \InvalidArgumentException();
            }
        }

        $types = array_keys($types);
        if (\count($types) === 1) {
            $type = $types[0];
        } else {
            if (\in_array(PropertyInfo\Type::BUILTIN_TYPE_STRING, $types, true)) {
                $type = PropertyInfo\Type::BUILTIN_TYPE_STRING;
            } elseif (\in_array(PropertyInfo\Type::BUILTIN_TYPE_FLOAT, $types, true)) {
                $type = PropertyInfo\Type::BUILTIN_TYPE_FLOAT;
            } else {
                $type = PropertyInfo\Type::BUILTIN_TYPE_INT;
            }
        }

        return new RestApiBundle\Model\Helper\TypeExtractor\EnumData($type, $values);
    }
}
