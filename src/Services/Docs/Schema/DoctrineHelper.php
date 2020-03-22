<?php

namespace RestApiBundle\Services\Docs\Schema;

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
}
