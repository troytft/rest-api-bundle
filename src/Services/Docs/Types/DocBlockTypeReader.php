<?php

namespace RestApiBundle\Services\Docs\Types;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection as PhpDoc;
use RestApiBundle;
use function count;

class DocBlockTypeReader extends RestApiBundle\Services\Docs\Types\BaseTypeReader
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function getMethodReturnSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Types\TypeInterface
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

        return $this->convertTypeToSchema($returnTag->getType(), false);
    }

    private function convertTypeToSchema(PhpDoc\Type $type, bool $nullable): RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        if ($type instanceof PhpDoc\Types\Null_) {
            $result = new RestApiBundle\DTO\Docs\Types\NullType();
        } elseif ($type instanceof PhpDoc\Types\Array_) {
            $result = $this->convertArrayTypeToSchema($type, $nullable);
        } elseif ($type instanceof PhpDoc\Types\Compound) {
            $result = $this->convertCompoundTypeToSchema($type);
        } else {
            $type = (string) $type;
            if ($this->isScalarType($type)) {
                $result = $this->createScalarTypeFromString($type, $nullable);
            } else {
                $result = $this->createClassTypeFromString($type, $nullable);
            }
        }

        return $result;
    }

    private function convertCompoundTypeToSchema(PhpDoc\Types\Compound $type): RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        $compoundTypes = (array) $type->getIterator();
        if (count($compoundTypes) !== 2) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        if ($compoundTypes[0] === $compoundTypes[1]) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        if (!$compoundTypes[0] instanceof PhpDoc\Types\Null_ && !$compoundTypes[1] instanceof PhpDoc\Types\Null_) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        $result = null;

        foreach ($compoundTypes as $compoundType) {
            if ($compoundType instanceof PhpDoc\Types\Null_) {
                continue;
            }

            $result = $this->convertTypeToSchema($compoundType, true);
        }

        if (!$result instanceof RestApiBundle\DTO\Docs\Types\TypeInterface) {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertArrayTypeToSchema(PhpDoc\Types\Array_ $type, bool $nullable): RestApiBundle\DTO\Docs\Types\ArrayType
    {
        $schemaType = $this->convertTypeToSchema($type->getValueType(), false);

        return new RestApiBundle\DTO\Docs\Types\ArrayType($schemaType, $nullable);
    }
}
