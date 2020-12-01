<?php

namespace RestApiBundle\Services\OpenApi;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Constraint;
use RestApiBundle;
use Mapper;
use function sprintf;

class RequestModelHelper
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var Mapper\SchemaGenerator
     */
    private $schemaGenerator;

    /**
     * @var RestApiBundle\Services\OpenApi\DoctrineHelper
     */
    private $doctrineHelper;

    public function __construct(
        RestApiBundle\Services\Request\MapperInitiator $mapperInitiator,
        RestApiBundle\Services\OpenApi\DoctrineHelper $doctrineHelper
    ) {
        $this->annotationReader = Mapper\Helper\AnnotationReaderFactory::create(true);
        $this->schemaGenerator = $mapperInitiator->getMapper()->getSchemaGenerator();
        $this->doctrineHelper = $doctrineHelper;
    }

    public function getSchemaByClass(string $class): RestApiBundle\DTO\OpenApi\Schema\ObjectType
    {
        $objectType = $this->convertObjectType($this->schemaGenerator->getSchemaByClassName($class));

        $this->applyValidationConstraints($objectType, $class);

        return $objectType;
    }


    private function convert(Mapper\DTO\Schema\TypeInterface $type): RestApiBundle\DTO\OpenApi\Schema\TypeInterface
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

    private function convertScalar(Mapper\DTO\Schema\ScalarTypeInterface $scalarType): RestApiBundle\DTO\OpenApi\Schema\TypeInterface
    {
        switch ($scalarType->getTransformerName()) {
            case Mapper\Transformer\BooleanTransformer::getName():
                $result = new RestApiBundle\DTO\OpenApi\Schema\BooleanType($scalarType->getNullable());

                break;

            case Mapper\Transformer\IntegerTransformer::getName():
                $result = new RestApiBundle\DTO\OpenApi\Schema\IntegerType($scalarType->getNullable());

                break;

            case Mapper\Transformer\StringTransformer::getName():
                $result = new RestApiBundle\DTO\OpenApi\Schema\StringType($scalarType->getNullable());

                break;

            case Mapper\Transformer\FloatTransformer::getName():
                $result = new RestApiBundle\DTO\OpenApi\Schema\FloatType($scalarType->getNullable());

                break;

            case Mapper\Transformer\DateTimeTransformer::getName():
                $result = new RestApiBundle\DTO\OpenApi\Schema\DateTimeType($scalarType->getNullable());

                break;

            case Mapper\Transformer\DateTransformer::getName():
                $result = new RestApiBundle\DTO\OpenApi\Schema\DateType($scalarType->getNullable());

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
                $result = new RestApiBundle\DTO\OpenApi\Schema\ArrayType($innerSchema, $scalarType->getNullable());

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Invalid type "%s"', $scalarType->getTransformerName()));
        }

        return $result;
    }

    private function convertObjectType(Mapper\DTO\Schema\ObjectTypeInterface $objectType): RestApiBundle\DTO\OpenApi\Schema\ObjectType
    {
        $properties = [];

        foreach ($objectType->getProperties() as $name => $property) {
            $properties[$name] = $this->convert($property);
        }

        return new RestApiBundle\DTO\OpenApi\Schema\ObjectType($properties, $objectType->getNullable());
    }

    private function convertCollectionType(Mapper\DTO\Schema\CollectionTypeInterface $collectionType): RestApiBundle\DTO\OpenApi\Schema\ArrayType
    {
        return new RestApiBundle\DTO\OpenApi\Schema\ArrayType($this->convert($collectionType->getItems()), $collectionType->getNullable());
    }
}
