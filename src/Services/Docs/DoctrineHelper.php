<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use Doctrine;
use function array_key_last;
use function explode;
use function sprintf;

class DoctrineHelper
{
    /**
     * @var RestApiBundle\Services\Docs\SilentAnnotationReader
     */
    private $silentAnnotationReader;

    public function __construct(RestApiBundle\Services\Docs\SilentAnnotationReader $silentAnnotationReader)
    {
        $this->silentAnnotationReader = $silentAnnotationReader;
    }

    public function isEntity(string $class): bool
    {
        $reflectionClass = RestApiBundle\Services\ReflectionClassStore::get($class);

        return (bool) $this->silentAnnotationReader->getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }

    public function getEntityFieldSchema(string $class, string $field, bool $nullable): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $reflectionClass = RestApiBundle\Services\ReflectionClassStore::get($class);
        $reflectionProperty = $reflectionClass->getProperty($field);

        $columnAnnotation = $this->silentAnnotationReader->getPropertyAnnotation($reflectionProperty, Doctrine\ORM\Mapping\Column::class);
        if (!$columnAnnotation instanceof Doctrine\ORM\Mapping\Column) {
            throw new \InvalidArgumentException();
        }

        $description = $this->resolveDescription($class, $field);

        switch ($columnAnnotation->type) {
            case 'string':
                $schema = new RestApiBundle\DTO\Docs\Schema\StringType($nullable);
                $schema
                    ->setDescription($description);

                break;

            case 'integer':
                $schema = new RestApiBundle\DTO\Docs\Schema\IntegerType($nullable);
                $schema
                    ->setDescription($description);

                break;

            default:
                throw new \InvalidArgumentException('Not implemented.');
        }

        return $schema;
    }

    private function resolveDescription(string $class, string $field): string
    {
        $parts = explode('\\', $class);
        $name = $parts[array_key_last($parts)];

        return sprintf('Entity "%s" by field "%s"', $name, $field);
    }
}
