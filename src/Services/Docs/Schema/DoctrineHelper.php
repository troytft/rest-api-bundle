<?php

namespace RestApiBundle\Services\Docs\Schema;

use Doctrine\ORM\Mapping\ClassMetadata;
use RestApiBundle;
use Doctrine\ORM\EntityManagerInterface;

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

    public function getEntityFieldSchema(string $className, string $fieldName): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($className);
        if (!$metadata instanceof ClassMetadata) {
            throw new \InvalidArgumentException();
        }

        $fieldMetadata = $metadata->getFieldMapping($fieldName);
        if (empty($fieldMetadata['type'])) {
            throw new \InvalidArgumentException();
        }

        switch ($fieldMetadata['type']) {
            case 'string':
                $schema = new RestApiBundle\DTO\Docs\Schema\StringType(false);

                break;

            case 'integer':
                $schema = new RestApiBundle\DTO\Docs\Schema\IntegerType(false);

                break;

            default:
                throw new \InvalidArgumentException('Not implemented.');
        }

        return $schema;
    }
}
