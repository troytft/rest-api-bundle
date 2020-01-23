<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;

class ReturnTypeToSchemaConverter
{
    public function convert(RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface $returnType): OpenApi\Schema
    {
        if ($returnType instanceof RestApiBundle\DTO\Docs\ReturnType\ObjectType) {
            $result = $this->convertObjectType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\ReturnType\CollectionType) {
            $result = $this->convertCollectionType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\ReturnType\StringType) {
            $result = $this->convertStringType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\ReturnType\IntegerType) {
            $result = $this->convertIntegerType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\ReturnType\FloatType) {
            $result = $this->convertFloatType($returnType);
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\ReturnType\BooleanType) {
            $result = $this->convertBooleanType($returnType);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertObjectType(RestApiBundle\DTO\Docs\ReturnType\ObjectType $objectType): OpenApi\Schema
    {
        $properties = [];

        foreach ($objectType->getProperties() as $key => $propertyType) {
            $properties[$key] = $this->convert($propertyType);
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'nullable' => $objectType->getIsNullable(),
            'properties' => $properties,
        ]);
    }

    private function convertCollectionType(RestApiBundle\DTO\Docs\ReturnType\CollectionType $collectionType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $collectionType->getIsNullable(),
            'items' => [
                $this->convert($collectionType->getType())
            ]
        ]);
    }

    private function convertStringType(RestApiBundle\DTO\Docs\ReturnType\StringType $stringType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => $stringType->getIsNullable(),
        ]);
    }

    private function convertIntegerType(RestApiBundle\DTO\Docs\ReturnType\IntegerType $integerType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::INTEGER,
            'nullable' => $integerType->getIsNullable(),
        ]);
    }

    private function convertFloatType(RestApiBundle\DTO\Docs\ReturnType\FloatType $floatType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::NUMBER,
            'format' => 'double',
            'nullable' => $floatType->getIsNullable(),
        ]);
    }

    private function convertBooleanType(RestApiBundle\DTO\Docs\ReturnType\BooleanType $booleanType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::BOOLEAN,
            'nullable' => $booleanType->getIsNullable(),
        ]);
    }
}
