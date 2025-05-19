<?php declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\SchemaTypeResolver;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class UploadedFileTypeResolver implements SchemaTypeResolverInterface
{
    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function supports(PropertyInfo\Type $propertyInfoType, array $typeOptions): bool
    {
        return $propertyInfoType->getClassName() && RestApiBundle\Helper\ReflectionHelper::isUploadedFile($propertyInfoType->getClassName());
    }

    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    public function resolve(PropertyInfo\Type $propertyInfoType, array $typeOptions): RestApiBundle\Model\Mapper\Schema
    {
        return RestApiBundle\Model\Mapper\Schema::createUploadedFileType($propertyInfoType->isNullable());
    }
}
