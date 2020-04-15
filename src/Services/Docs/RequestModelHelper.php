<?php

namespace RestApiBundle\Services\Docs;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use RestApiBundle;
use Mapper;
use function array_merge;
use function sprintf;
use function var_dump;

class RequestModelHelper
{
    /**
     * @var Mapper\SchemaGenerator
     */
    private $schemaGenerator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var RestApiBundle\Services\Docs\DoctrineHelper
     */
    private $doctrineHelper;

    public function __construct(
        RestApiBundle\Services\Request\MapperInitiator $mapperInitiator,
        ValidatorInterface $validator,
        RestApiBundle\Services\Docs\DoctrineHelper $doctrineHelper
    ) {
        $this->schemaGenerator = $mapperInitiator->getMapper()->getSchemaGenerator();
        $this->validator = $validator;
        $this->doctrineHelper = $doctrineHelper;
    }

    public function getSchemaByClass(string $class): RestApiBundle\DTO\Docs\Schema\ObjectType
    {
        $objectType = $this->convertObjectType($this->schemaGenerator->getSchemaByClassName($class));
        $validationClassMetadata = $this->validator->getMetadataFor($class);

        if ($validationClassMetadata instanceof ClassMetadata) {
            foreach ($objectType->getProperties() as $name => $property) {
                if (!$property instanceof RestApiBundle\DTO\Docs\Schema\ValidationAwareInterface) {
                    continue;
                }

                $propertyMetadataArray = $validationClassMetadata->getPropertyMetadata($name);

                /** @var PropertyMetadata $propertyMetadata */
                foreach ($propertyMetadataArray as $propertyMetadata) {
                    $property->setConstraints(array_merge($property->getConstraints(), $propertyMetadata->getConstraints()));
                }
            }
        } elseif ($validationClassMetadata !== null) {
            throw new \RuntimeException();
        }

        return $objectType;
    }

    private function convert(Mapper\DTO\Schema\TypeInterface $type): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        if ($type instanceof Mapper\DTO\Schema\ObjectTypeInterface) {
            $result = $this->convertObjectType($type);
        } elseif ($type instanceof Mapper\DTO\Schema\ScalarTypeInterface) {
            $result = $this->convertScalar($type);
        } elseif ($type instanceof Mapper\DTO\Schema\CollectionTypeInterface) {
            $result = $this->convertCollectionType($type);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertScalar(Mapper\DTO\Schema\ScalarTypeInterface $scalarType): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        switch ($scalarType->getTransformerName()) {
            case Mapper\Transformer\BooleanTransformer::getName():
                $result = new RestApiBundle\DTO\Docs\Schema\BooleanType($scalarType->getNullable());

                break;

            case Mapper\Transformer\IntegerTransformer::getName():
                $result = new RestApiBundle\DTO\Docs\Schema\IntegerType($scalarType->getNullable());

                break;

            case Mapper\Transformer\StringTransformer::getName():
                $result = new RestApiBundle\DTO\Docs\Schema\StringType($scalarType->getNullable());

                break;

            case Mapper\Transformer\FloatTransformer::getName():
                $result = new RestApiBundle\DTO\Docs\Schema\FloatType($scalarType->getNullable());

                break;

            case Mapper\Transformer\DateTimeTransformer::getName():
                $result = new RestApiBundle\DTO\Docs\Schema\DateTimeType($scalarType->getNullable());

                break;

            case Mapper\Transformer\DateTransformer::getName():
                $result = new RestApiBundle\DTO\Docs\Schema\DateType($scalarType->getNullable());

                break;

            case RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::getName():
                $className = $scalarType->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::CLASS_OPTION];
                $fieldName = $scalarType->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntityTransformer::FIELD_OPTION];

                $result = $this->doctrineHelper->getEntityFieldSchema($className, $fieldName, $scalarType->getNullable());

                break;

            case RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::getName():
                $className = $scalarType->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::CLASS_OPTION];
                $fieldName = $scalarType->getTransformerOptions()[RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::FIELD_OPTION];

                $innerSchema = $this->doctrineHelper->getEntityFieldSchema($className, $fieldName, false);
                $result = new RestApiBundle\DTO\Docs\Schema\ArrayType($innerSchema, $scalarType->getNullable());

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Invalid type "%s"', $scalarType->getTransformerName()));
        }

        return $result;
    }

    private function convertObjectType(Mapper\DTO\Schema\ObjectTypeInterface $objectType): RestApiBundle\DTO\Docs\Schema\ObjectType
    {
        $properties = [];

        foreach ($objectType->getProperties() as $name => $property) {
            $properties[$name] = $this->convert($property);
        }

        return new RestApiBundle\DTO\Docs\Schema\ObjectType($properties, $objectType->getNullable());
    }

    private function convertCollectionType(Mapper\DTO\Schema\CollectionTypeInterface $collectionType): RestApiBundle\DTO\Docs\Schema\ArrayType
    {
        return new RestApiBundle\DTO\Docs\Schema\ArrayType($this->convert($collectionType->getItems()), $collectionType->getNullable());
    }
}