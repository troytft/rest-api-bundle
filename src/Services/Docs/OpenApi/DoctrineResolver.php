<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use Mapper\Helper\AnnotationReaderFactory;
use cebe\openapi\spec as OpenApi;
use RestApiBundle;
use Doctrine;
use function array_key_last;
use function explode;
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

    public function getEntityFieldSchema(string $class, string $field, bool $nullable): OpenApi\Schema
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $reflectionProperty = $reflectionClass->getProperty($field);

        $columnAnnotation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, Doctrine\ORM\Mapping\Column::class);
        if (!$columnAnnotation instanceof Doctrine\ORM\Mapping\Column) {
            throw new \InvalidArgumentException();
        }

        $description = $this->resolveDescription($class, $field);

        switch ($columnAnnotation->type) {
            case 'string':
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $nullable,
                    'description' => $description,
                ]);

                break;

            case 'integer':
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $nullable,
                    'description' => $description,
                ]);

                break;

            default:
                throw new \InvalidArgumentException('Not implemented.');
        }

        return $result;
    }

    private function resolveDescription(string $class, string $field): string
    {
        $parts = explode('\\', $class);
        $name = $parts[array_key_last($parts)];

        return sprintf('Entity "%s" by field "%s"', $name, $field);
    }
}
