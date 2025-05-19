<?php declare(strict_types=1);

namespace Tests\Fixture\TestApp\Repository;

use Tests;

use function array_values;
use function in_array;

class BookRepository extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository
{
    public function __construct(\Doctrine\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Tests\Fixture\TestApp\Entity\Book::class);
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Tests\Fixture\TestApp\Entity\Book
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?Tests\Fixture\TestApp\Entity\Book
    {
        $result = null;

        $criteriaId = $criteria['id'] ?? null;
        if ($criteriaId) {
            foreach ($this->findAll() as $book) {
                if ($book->getId() === $criteriaId) {
                    $result = $book;
                }
            }
        }

        $criteriaSlug = $criteria['slug'] ?? null;
        if ($criteriaSlug) {
            foreach ($this->findAll() as $book) {
                if ($book->getSlug() === $criteriaSlug) {
                    $result = $book;
                }
            }
        }

        return $result;
    }

    /**
     * @return Tests\Fixture\TestApp\Entity\Book[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $result = [];

        $criteriaId = $criteria['id'] ?? null;
        if ($criteriaId) {
            foreach ($this->findAll() as $book) {
                if (in_array($book->getId(), $criteriaId, true)) {
                    $result[$book->getId()] = $book;
                }
            }
        }

        $criteriaSlug = $criteria['slug'] ?? null;
        if ($criteriaSlug) {
            foreach ($this->findAll() as $book) {
                if (in_array($book->getSlug(), $criteriaSlug, true)) {
                    $result[$book->getId()] = $book;
                }
            }
        }


        return array_values($result);
    }

    /**
     * @return Tests\Fixture\TestApp\Entity\Book[]
     */
    public function findAll(): array
    {
        return [
            new Tests\Fixture\TestApp\Entity\Book(1, 'keto-cookbook-beginners-low-carb-homemade', 'Keto Cookbook For Beginners: 1000 Recipes For Quick & Easy Low-Carb Homemade Cooking'),
            new Tests\Fixture\TestApp\Entity\Book(2, 'design-ideas-making-house-home', 'Home Stories: Design Ideas for Making a House a Home')
        ];
    }
}
