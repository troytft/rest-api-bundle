<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\SchemaTypeResolver;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class PolyfillEnumTypeResolver implements SchemaTypeResolverInterface
{
    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function supports(PropertyInfo\Type $propertyInfoType, array $typeOptions): bool
    {
        return $propertyInfoType->getClassName() && RestApiBundle\Helper\ReflectionHelper::isMapperEnum($propertyInfoType->getClassName());
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
