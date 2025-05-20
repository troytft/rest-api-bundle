<?php

declare(strict_types=1);

namespace Tests\Fixture\TestApp\Repository;

use Tests;

class AuthorRepository extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository
{
    /**
     * @var int[]
     */
    private array $existIds = [1, 2];

    public function __construct(\Doctrine\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Tests\Fixture\TestApp\Entity\Author::class);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?Tests\Fixture\TestApp\Entity\Author
    {
        if (isset($criteria['id']) && \in_array($criteria['id'], $this->existIds, true)) {
            return $this->createEntityWithId($criteria['id']);
        }

        return null;
    }

    /**
     * @return Tests\Fixture\TestApp\Entity\Author[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $result = [];

        if (isset($criteria['id'])) {
            foreach ($criteria['id'] as $id) {
                if (\in_array($id, $this->existIds, true)) {
                    $result[] = $this->createEntityWithId($id);
                }
            }
        }

        return $result;
    }

    private function createEntityWithId(int $id): Tests\Fixture\TestApp\Entity\Author
    {
        $genre = new Tests\Fixture\TestApp\Entity\Author();
        $genre
            ->setId($id);

        return $genre;
    }
}
