<?php

namespace RestApiBundle\Services\Docs\Schema;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use RestApiBundle;
use Mapper;
use function array_merge;

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

    public function __construct(
        RestApiBundle\Services\Request\MapperInitiator $mapperInitiator,
        ValidatorInterface $validator
    ) {
        $this->schemaGenerator = $mapperInitiator->getMapper()->getSchemaGenerator();
        $this->validator = $validator;
    }

    public function getSchemaByClass(string $class): RestApiBundle\DTO\Docs\Schema\ObjectType
    {
        $objectType = $this->convertObjectType($this->schemaGenerator->getSchemaByClassName($class));
        $validationClassMetadata = $this->validator->getMetadataFor($class);

        if ($validationClassMetadata instanceof ClassMetadata) {
            foreach ($objectType->getProperties() as $name => $property) {
                if (!$property instanceof RestApiBundle\DTO\Docs\Schema\ScalarInterface) {
                    continue;
                }

                $propertyMetadataArray = $validationClassMetadata->getPropertyMetadata($name);

                /** @var PropertyMetadata $propertyMetadata */
                foreach ($propertyMetadataArray as $propertyMetadata) {
                    $property->setConstraints(array_merge($property->getConstraints(), $propertyMetadata->getConstraints()));
                }
            }
        } elseif ($validationClassMetadata) {
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

            default:
                throw new \InvalidArgumentException();
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
