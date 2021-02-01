<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use Mapper\Helper\AnnotationReaderFactory;
use RestApiBundle;
use Doctrine;
use cebe\openapi\spec as OpenApi;

use function sprintf;

class DoctrineResolver extends RestApiBundle\Services\Docs\OpenApi\AbstractSchemaResolver
{
    /**
     * @var Doctrine\Common\Annotations\AnnotationReader
     */
    private $annotationReader;

    public function __construct()
    {
        $this->annotationReader = AnnotationReaderFactory::create(true);
    }

    public function isEntity(string $class): bool
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return (bool) $this->annotationReader->getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }

    public function resolveByColumnType(string $class, string $field): OpenApi\Schema
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $reflectionProperty = $reflectionClass->getProperty($field);

        $columnAnnotation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, Doctrine\ORM\Mapping\Column::class);
        if (!$columnAnnotation instanceof Doctrine\ORM\Mapping\Column) {
            throw new \InvalidArgumentException();
        }

        switch ($columnAnnotation->type) {
            case 'integer':
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                ]);

                break;

            case 'string':
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                ]);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unknown column type %s', $columnAnnotation->type));
        }

        return $result;
    }
}
