<?php

namespace RestApiBundle\Services\Docs\Schema;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use RestApiBundle;
use function count;
use function ltrim;

class DocBlockSchemaReader
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function getFunctionReturnSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Schema\TypeInterface
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

        return $this->convertTypeToSchema($returnTag->getType());
    }

    private function convertTypeToSchema(Type $type): RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        if ($type instanceof Null_) {
            $result = new RestApiBundle\DTO\Docs\Schema\NullType();
        } elseif ($type instanceof Object_) {
            $result = $this->convertObjectTypeToSchema($type, false);
        } elseif ($type instanceof Array_) {
            $result = $this->convertArrayTypeToSchema($type, false);
        } elseif ($type instanceof Compound) {
            $result = $this->convertCompoundTypeToSchema($type);
        } else {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return $result;
    }

    private function convertCompoundTypeToSchema(Compound $type): RestApiBundle\DTO\Docs\Schema\TypeInterface
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
                $result = $this->convertObjectTypeToSchema($compoundType, true);
            } elseif ($compoundType instanceof Array_) {
                $result = $this->convertArrayTypeToSchema($compoundType, true);
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

    private function convertObjectTypeToSchema(Object_ $type, bool $isNullable): RestApiBundle\DTO\Docs\Schema\ClassType
    {
        $class = ltrim((string) $type, '\\');

        return new RestApiBundle\DTO\Docs\Schema\ClassType($class, $isNullable);
    }

    private function convertArrayTypeToSchema(Array_ $type, bool $isNullable): RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType
    {
        $valueType = $type->getValueType();
        if (!$valueType instanceof Object_) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        $classType = $this->convertObjectTypeToSchema($valueType, $isNullable);

        return new RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType($classType->getClass(), $isNullable);
    }
}
