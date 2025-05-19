<?php

namespace RestApiBundle\Services\Mapper\SchemaTypeResolver;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class DoctrineEntityArrayTypeResolver implements SchemaTypeResolverInterface
{
    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function supports(PropertyInfo\Type $propertyInfoType, array $typeOptions): bool
    {
        if (!$propertyInfoType->isCollection()) {
            return false;
        }

        $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($propertyInfoType);

        return $collectionValueType->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($collectionValueType->getClassName());
    }

    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function resolve(PropertyInfo\Type $propertyInfoType, array $typeOptions): RestApiBundle\Model\Mapper\Schema
    {
        $fieldName = 'id';
        foreach ($typeOptions as $typeOption) {
            if ($typeOption instanceof RestApiBundle\Mapping\Mapper\FindByField) {
                $fieldName = $typeOption->getField();
            }
        }

        $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($propertyInfoType);

        return RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class, $propertyInfoType->isNullable(), [
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::CLASS_OPTION => $collectionValueType->getClassName(),
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::FIELD_OPTION => $fieldName,
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::MULTIPLE_OPTION => true,
        ]);
    }
}
