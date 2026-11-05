<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\SchemaTypeResolver;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class DoctrineEntityArrayTypeResolver implements SchemaTypeResolverInterface
{
    public function __construct(
        private RestApiBundle\Services\PropertyTypeExtractorService $propertyTypeExtractorService,
    ) {
    }

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
        $class = $collectionValueType->getClassName();
        $valueTransformerClass = match ($this->propertyTypeExtractorService->getTypeRequired($class, $fieldName)->getBuiltinType()) {
            PropertyInfo\Type::BUILTIN_TYPE_INT => RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class,
            PropertyInfo\Type::BUILTIN_TYPE_STRING => RestApiBundle\Services\Mapper\Transformer\StringTransformer::class,
            default => throw new RestApiBundle\Exception\Schema\InvalidDefinitionException(\sprintf('Field "%s" of entity "%s" must be integer or string.', $fieldName, $class)),
        };

        return RestApiBundle\Model\Mapper\Schema::createArrayType(
            RestApiBundle\Model\Mapper\Schema::createTransformerType($valueTransformerClass, false),
            $propertyInfoType->isNullable(),
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class,
            [
                RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::CLASS_OPTION => $class,
                RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::FIELD_OPTION => $fieldName,
                RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::MULTIPLE_OPTION => true,
            ],
        );
    }
}
