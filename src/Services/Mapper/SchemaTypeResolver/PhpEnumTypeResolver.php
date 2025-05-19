<?php

namespace RestApiBundle\Services\Mapper\SchemaTypeResolver;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class PhpEnumTypeResolver implements SchemaTypeResolverInterface
{
    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function supports(PropertyInfo\Type $propertyInfoType, array $typeOptions): bool
    {
        return $propertyInfoType->getClassName() && enum_exists($propertyInfoType->getClassName());
    }

    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function resolve(PropertyInfo\Type $propertyInfoType, array $typeOptions): RestApiBundle\Model\Mapper\Schema
    {
        return RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\EnumTransformer::class, $propertyInfoType->isNullable(), [
            RestApiBundle\Services\Mapper\Transformer\EnumTransformer::CLASS_OPTION => $propertyInfoType->getClassName(),
        ]);
    }
}
