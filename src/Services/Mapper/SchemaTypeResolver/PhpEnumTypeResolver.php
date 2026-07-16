<?php

declare(strict_types=1);

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
        return $propertyInfoType->getClassName() && \enum_exists($propertyInfoType->getClassName());
    }

    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function resolve(PropertyInfo\Type $propertyInfoType, array $typeOptions): RestApiBundle\Model\Mapper\Schema
    {
        $class = $propertyInfoType->getClassName() ?? throw new \InvalidArgumentException();
        if (!\is_subclass_of($class, \BackedEnum::class)) {
            throw new RestApiBundle\Exception\Schema\InvalidDefinitionException(\sprintf('Enum "%s" must be backed by int or string.', $class));
        }

        return RestApiBundle\Model\Mapper\Schema::createTransformerType(RestApiBundle\Services\Mapper\Transformer\EnumTransformer::class, $propertyInfoType->isNullable(), [
            RestApiBundle\Services\Mapper\Transformer\EnumTransformer::CLASS_OPTION => $class,
            RestApiBundle\Services\Mapper\Transformer\EnumTransformer::BACKING_TYPE_OPTION => RestApiBundle\Helper\TypeExtractor::extractEnumData($class)->type,
        ]);
    }
}
