<?php declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\SchemaTypeResolver;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class StringTypeResolver implements SchemaTypeResolverInterface
{
    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function supports(PropertyInfo\Type $propertyInfoType, array $typeOptions): bool
    {
        return $propertyInfoType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING;
    }

    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function resolve(PropertyInfo\Type $propertyInfoType, array $typeOptions): RestApiBundle\Model\Mapper\Schema
    {
        $hasTrimOption = null;
        $hasEmptyToNullOption = null;

        foreach ($typeOptions as $typeOption) {
            if ($typeOption instanceof RestApiBundle\Mapping\Mapper\Trim) {
                $hasTrimOption = true;
            }

            if ($typeOption instanceof RestApiBundle\Mapping\Mapper\EmptyToNull) {
                $hasEmptyToNullOption = true;
            }
        }

        return RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $propertyInfoType->isNullable(), [
            RestApiBundle\Services\Mapper\Transformer\StringTransformer::TRIM_OPTION => $hasTrimOption,
            RestApiBundle\Services\Mapper\Transformer\StringTransformer::EMPTY_TO_NULL_OPTION => $hasEmptyToNullOption,
        ]);
    }
}
