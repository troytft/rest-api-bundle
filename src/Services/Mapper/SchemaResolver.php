<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class SchemaResolver implements SchemaResolverInterface
{
    private array $schemaTypeResolvers;

    public function __construct(
        private RestApiBundle\Services\PropertyTypeExtractorService $propertyTypeExtractorService,
    ) {
        $this->schemaTypeResolvers = [
            new SchemaTypeResolver\StringTypeResolver(),
            new SchemaTypeResolver\IntegerTypeResolver(),
            new SchemaTypeResolver\FloatTypeResolver(),
            new SchemaTypeResolver\BooleanTypeResolver(),
            new SchemaTypeResolver\PhpEnumTypeResolver(),
            new SchemaTypeResolver\PolyfillEnumTypeResolver(),
            new SchemaTypeResolver\DoctrineEntityTypeResolver(),
            new SchemaTypeResolver\DoctrineEntityArrayTypeResolver(),
            new SchemaTypeResolver\DateTypeResolver(),
            new SchemaTypeResolver\DateTimeTypeResolver(),
            new SchemaTypeResolver\UploadedFileTypeResolver(),
        ];
    }

    public function resolve(string $class, bool $isNullable = false): RestApiBundle\Model\Mapper\Schema
    {
        $properties = [];
        $reflectionClass = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class);
        $isExposedAll = RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionClass, RestApiBundle\Mapping\Mapper\ExposeAll::class) instanceof RestApiBundle\Mapping\Mapper\ExposeAll;

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $isExposed = false;
            $propertyOptions = [];
            $propertyAnnotations = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotations($reflectionProperty);

            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\Expose) {
                    $isExposed = true;
                } elseif ($propertyAnnotation instanceof RestApiBundle\Mapping\Mapper\PropertyOptionInterface) {
                    $propertyOptions[] = $propertyAnnotation;
                }
            }

            if (!$isExposed && !$isExposedAll) {
                continue;
            }

            try {
                $propertyType = $this->propertyTypeExtractorService->getTypeRequired($class, $reflectionProperty->getName());
                $propertySchema = $this->resolveSchemaByType($propertyType, $propertyOptions);

                if (!$reflectionProperty->isPublic()) {
                    $formattedPropertyName = \ucfirst($reflectionProperty->getName());
                    $propertySchema->propertySetterName = 'set' . $formattedPropertyName;
                    if (!$reflectionClass->hasMethod($propertySchema->propertySetterName) || !$reflectionClass->getMethod($propertySchema->propertySetterName)->isPublic()) {
                        throw new RestApiBundle\Exception\Schema\InvalidDefinitionException(\sprintf('Property "%s" must be public or setter must exist.', $reflectionProperty->getName()));
                    }

                    $propertySchema->propertyGetterName = 'get' . $formattedPropertyName;
                    if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                        $propertySchema->propertyGetterName = $reflectionProperty->getName();
                        if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                            $propertySchema->propertyGetterName = 'is' . $formattedPropertyName;
                            if (!$reflectionClass->hasMethod($propertySchema->propertyGetterName) || !$reflectionClass->getMethod($propertySchema->propertyGetterName)->isPublic()) {
                                throw new RestApiBundle\Exception\Schema\InvalidDefinitionException(\sprintf('Property "%s" must be public or getter must exist.', $reflectionProperty->getName()));
                            }
                        }
                    }
                }
            } catch (RestApiBundle\Exception\Schema\InvalidDefinitionException $exception) {
                throw new RestApiBundle\Exception\ContextAware\PropertyAwareException($exception->getMessage(), $reflectionProperty->class, $reflectionProperty->name, $exception);
            }

            $properties[$reflectionProperty->getName()] = $propertySchema;
        }

        return RestApiBundle\Model\Mapper\Schema::createModelType($class, $properties, $isNullable);
    }

    /**
     * @param RestApiBundle\Mapping\Mapper\PropertyOptionInterface[] $typeOptions
     */
    private function resolveSchemaByType(PropertyInfo\Type $propertyInfoType, array $typeOptions = []): RestApiBundle\Model\Mapper\Schema
    {
        $schema = null;
        foreach ($this->schemaTypeResolvers as $schemaTypeResolver) {
            if ($schemaTypeResolver->supports($propertyInfoType, $typeOptions)) {
                $schema = $schemaTypeResolver->resolve($propertyInfoType, $typeOptions);

                break;
            }
        }

        if (!$schema) {
            if ($propertyInfoType->getClassName() && RestApiBundle\Helper\ReflectionHelper::isMapperModel($propertyInfoType->getClassName())) {
                $schema = $this->resolve($propertyInfoType->getClassName(), $propertyInfoType->isNullable());
            } elseif ($propertyInfoType->isCollection()) {
                $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($propertyInfoType);
                $collectionValueSchema = $this->resolveSchemaByType($collectionValueType, $typeOptions);
                $schema = RestApiBundle\Model\Mapper\Schema::createArrayType($collectionValueSchema, $propertyInfoType->isNullable());
            } else {
                throw new \LogicException(\sprintf('Unknown type: %s', RestApiBundle\Helper\PropertyInfoHelper::format($propertyInfoType)));
            }
        }

        return $schema;
    }
}
