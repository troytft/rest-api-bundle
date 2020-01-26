<?php

namespace RestApiBundle\Services\Docs\Type;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use RestApiBundle;
use function count;
use function ltrim;

class DocBlockReader
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function getReturnTypeByReturnTag(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        if (!$reflectionMethod->getDocComment()) {
            return null;
        }

        $docBlock = $this->docBlockFactory->create($reflectionMethod->getDocComment());

        $count = count($docBlock->getTagsByName('return'));

        if ($count === 0) {
            return null;
        }

        if ($count > 1) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\TwoOrMoreReturnTagsException();
        }

        $returnTag = $docBlock->getTagsByName('return')[0];
        if (!$returnTag instanceof Return_) {
            throw new \InvalidArgumentException();
        }

        $type = $returnTag->getType();

        if ($type instanceof Null_) {
            $result = $this->convertNullTypeToReturnType($type);
        } elseif ($type instanceof Object_) {
            $result = $this->convertObjectTypeToClassType($type, false);
        } elseif ($type instanceof Array_) {
            $result = $this->convertArrayTypeToClassesCollectionType($type, false);
        } elseif ($type instanceof Compound) {
            $result = $this->convertCompoundTypeToReturnType($type);
        } else {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return $result;
    }

    private function convertNullTypeToReturnType(Null_ $type)
    {
        return new RestApiBundle\DTO\Docs\Type\NullType();
    }

    private function convertCompoundTypeToReturnType(Compound $type): RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        $compoundTypes = (array) $type->getIterator();
        if (count($compoundTypes) > 2) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        if ($compoundTypes[0] === $compoundTypes[1]) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        if (!$compoundTypes[0] instanceof Null_ && !$compoundTypes[1] instanceof Null_) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        $result = null;

        foreach ($compoundTypes as $compoundType) {
            if ($compoundType instanceof Object_) {
                $result = $this->convertObjectTypeToClassType($compoundType, true);
            } elseif ($compoundType instanceof Array_) {
                $result = $this->convertArrayTypeToClassesCollectionType($compoundType, true);
            } elseif ($compoundType instanceof Null_) {
                continue;
            } else {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }
        }

        if (!$result) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return $result;
    }

    private function convertObjectTypeToClassType(Object_ $type, bool $isNullable)
    {
        $class = ltrim((string) $type, '\\');

        return new RestApiBundle\DTO\Docs\Type\ClassType($class, $isNullable);
    }

    private function convertArrayTypeToClassesCollectionType(Array_ $type, bool $isNullable)
    {
        $valueType = $type->getValueType();
        if (!$valueType instanceof Object_) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        $classType = $this->convertObjectTypeToClassType($valueType, $isNullable);

        return new RestApiBundle\DTO\Docs\Type\ClassesCollectionType($classType->getClass(), $isNullable);
    }
}
