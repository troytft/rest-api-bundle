<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;

class TypeToSchemaConverter
{
    public function convert(RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface $returnType): OpenApi\Schema
    {
        if ($returnType instanceof RestApiBundle\DTO\Docs\Schema\ObjectType) {
            $result = $this->convertObjectType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\Schema\ArrayType) {
            $result = $this->convertCollectionType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\Schema\StringType) {
            $result = $this->convertStringType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\Schema\IntegerType) {
            $result = $this->convertIntegerType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\Schema\FloatType) {
            $result = $this->convertFloatType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\Schema\BooleanType) {
            $result = $this->convertBooleanType($returnType);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertObjectType(RestApiBundle\DTO\Docs\Schema\ObjectType $objectType): OpenApi\Schema
    {
        $properties = [];

        foreach ($objectType->getProperties() as $key => $propertyType) {
            $properties[$key] = $this->convert($propertyType);
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'nullable' => $objectType->getNullable(),
            'properties' => $properties,
        ]);
    }

    private function convertCollectionType(RestApiBundle\DTO\Docs\Schema\ArrayType $collectionType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $collectionType->getNullable(),
            'items' => [
                $this->convert($collectionType->getInnerType())
            ]
        ]);
    }

    private function convertStringType(RestApiBundle\DTO\Docs\Schema\StringType $stringType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => $stringType->getNullable(),
        ]);
    }

    private function convertIntegerType(RestApiBundle\DTO\Docs\Schema\IntegerType $integerType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::INTEGER,
            'nullable' => $integerType->getNullable(),
        ]);
    }

    private function convertFloatType(RestApiBundle\DTO\Docs\Schema\FloatType $floatType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::NUMBER,
            'format' => 'double',
            'nullable' => $floatType->getNullable(),
        ]);
    }

    private function convertBooleanType(RestApiBundle\DTO\Docs\Schema\BooleanType $booleanType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::BOOLEAN,
            'nullable' => $booleanType->getNullable(),
        ]);
    }
}
