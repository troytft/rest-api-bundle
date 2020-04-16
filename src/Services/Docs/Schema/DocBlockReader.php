<?php

namespace RestApiBundle\Services\Docs\Schema;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use RestApiBundle;
use function count;

class DocBlockReader extends RestApiBundle\Services\Docs\Schema\BaseReader
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function getMethodReturnSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
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

    private function convertTypeToSchema(Type $type, bool $nullable): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        if ($type instanceof Null_) {
            $result = new RestApiBundle\DTO\Docs\Schema\NullType();
        } elseif ($type instanceof Array_) {
            $result = $this->convertArrayTypeToSchema($type, $nullable);
        } elseif ($type instanceof Compound) {
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

    private function convertCompoundTypeToSchema(Compound $type): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $compoundTypes = (array) $type->getIterator();
        if (count($compoundTypes) !== 2) {
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
            if ($compoundType instanceof Null_) {
                continue;
            }

            $result = $this->convertTypeToSchema($compoundType, true);
        }

        if (!$result instanceof RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface) {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertArrayTypeToSchema(Array_ $type, bool $nullable): RestApiBundle\DTO\Docs\Schema\ArrayType
    {
        $schemaType = $this->convertTypeToSchema($type->getValueType(), false);

        return new RestApiBundle\DTO\Docs\Schema\ArrayType($schemaType, $nullable);
    }
}
