<?php

namespace RestApiBundle\Services\Mapper\SchemaTypeResolver;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class DateTypeResolver implements SchemaTypeResolverInterface
{
    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function supports(PropertyInfo\Type $propertyInfoType, array $typeOptions): bool
    {
        return $propertyInfoType->getClassName() && RestApiBundle\Helper\ReflectionHelper::isMapperDate($propertyInfoType->getClassName());
    }

    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function resolve(PropertyInfo\Type $propertyInfoType, array $typeOptions): RestApiBundle\Model\Mapper\Schema
    {
        $dateFormatOption = null;
        foreach ($typeOptions as $typeOption) {
            if ($typeOption instanceof RestApiBundle\Mapping\Mapper\DateFormat) {
                $dateFormatOption = $typeOption->getFormat();
            }
        }

        return RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\DateTransformer::class, $propertyInfoType->isNullable(), [
            RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION => $dateFormatOption,
        ]);
    }
}
