<?php

namespace RestApiBundle\Services\Docs;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use RestApiBundle;
use function count;
use function ltrim;

class DocBlockHelper
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function getReturnTypeByReturnTag(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
    {
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
            $result = $this->convertObjectTypeToReturnType($type, false);
        } elseif ($type instanceof Array_) {
            $result = $this->convertArrayTypeToReturnType($type, false);
        } elseif ($type instanceof Compound) {
            $types = (array) $type->getIterator();
            if (count($types) > 2) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            if ($types[0] instanceof Object_ && $types[1] instanceof Null_) {
                $result = $this->convertObjectTypeToReturnType($types[0], true);
            } elseif ($types[1] instanceof Object_ && $types[0] instanceof Null_) {
                $result = $this->convertObjectTypeToReturnType($types[1], true);
            } elseif ($types[0] instanceof Array_ && $types[1] instanceof Null_) {
                $result = $this->convertArrayTypeToReturnType($types[0], true);
            } elseif ($types[1] instanceof Array_ && $types[0] instanceof Null_) {
                $result = $this->convertArrayTypeToReturnType($types[1], true);
            } else {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }
        } else {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return $result;
    }

    private function convertNullTypeToReturnType(Null_ $type)
    {
        return new RestApiBundle\DTO\Docs\ReturnType\NullType();
    }

    private function convertObjectTypeToReturnType(Object_ $type, bool $isNullable)
    {
        $class = ltrim((string) $type, '\\');
        if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($class)) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return new RestApiBundle\DTO\Docs\ReturnType\ClassType($class, $isNullable);
    }

    private function convertArrayTypeToReturnType(Array_ $type, bool $isNullable)
    {
        $valueType = $type->getValueType();
        if (!$valueType instanceof Object_) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        $objectReturnType = $this->convertObjectTypeToReturnType($valueType, $isNullable);

        return new RestApiBundle\DTO\Docs\ReturnType\CollectionOfClassesType($objectReturnType->getClass(), $isNullable);
    }
}
