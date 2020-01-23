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
     * @var RestApiBundle\Services\Docs\ResponseModelHelper
     */
    private $responseModelHelper;

    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct(RestApiBundle\Services\Docs\ResponseModelHelper $responseModelHelper)
    {
        $this->responseModelHelper = $responseModelHelper;
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function getReturnTypeByReturnTag(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
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
            $result = $this->convertObjectTypeToReturnType($type, false);
        } elseif ($type instanceof Array_) {
            $result = $this->convertArrayTypeToReturnType($type, false);
        } elseif ($type instanceof Compound) {
            $result = $this->convertCompoundTypeToReturnType($type);
        } else {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return $result;
    }

    private function convertNullTypeToReturnType(Null_ $type)
    {
        return new RestApiBundle\DTO\Docs\ReturnType\NullType();
    }

    private function convertCompoundTypeToReturnType(Compound $type): RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
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
                $result = $this->convertObjectTypeToReturnType($compoundType, true);
            } elseif ($compoundType instanceof Array_) {
                $result = $this->convertArrayTypeToReturnType($compoundType, true);
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

    private function convertObjectTypeToReturnType(Object_ $type, bool $isNullable)
    {
        $class = ltrim((string) $type, '\\');
        if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($class)) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        $responseModelObject = $this->responseModelHelper->extractReturnTypeObjectFromResponseModelClass($class);

        return new RestApiBundle\DTO\Docs\ReturnType\ObjectType($responseModelObject->getProperties(), $isNullable);
    }

    private function convertArrayTypeToReturnType(Array_ $type, bool $isNullable)
    {
        $valueType = $type->getValueType();
        if (!$valueType instanceof Object_) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        $objectReturnType = $this->convertObjectTypeToReturnType($valueType, $isNullable);

        return new RestApiBundle\DTO\Docs\ReturnType\CollectionType($objectReturnType, $isNullable);
    }
}
