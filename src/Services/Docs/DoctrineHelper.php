<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\ORM\Mapping\ClassMetadata;
use RestApiBundle;
use Doctrine\ORM\EntityManagerInterface;
use function array_key_last;
use function explode;
use function sprintf;
use function var_dump;

class DoctrineHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isEntity(string $className): bool
    {
        return !$this->entityManager->getMetadataFactory()->isTransient($className);
    }

    public function getEntityFieldSchema(string $class, string $field, bool $nullable): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($class);
        if (!$metadata instanceof ClassMetadata) {
            throw new \InvalidArgumentException();
        }

        $fieldMetadata = $metadata->getFieldMapping($field);
        if (empty($fieldMetadata['type'])) {
            throw new \InvalidArgumentException();
        }

        $description = $this->resolveDescription($class, $field);

        switch ($fieldMetadata['type']) {
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
