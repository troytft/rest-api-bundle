<?php

namespace RestApiBundle\Services\Mapper\SchemaTypeResolver;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class DoctrineEntityTypeResolver implements SchemaTypeResolverInterface
{
    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function supports(PropertyInfo\Type $propertyInfoType, array $typeOptions): bool
    {
        return $propertyInfoType->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($propertyInfoType->getClassName());
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

        return RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class, $propertyInfoType->isNullable(), [
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::CLASS_OPTION => $propertyInfoType->getClassName(),
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::FIELD_OPTION => $fieldName,
        ]);
    }
}
