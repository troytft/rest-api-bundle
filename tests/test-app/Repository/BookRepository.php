<?php

namespace TestApp\Repository;

use TestApp;
use Doctrine\ORM\EntityRepository;

use function array_unique;

class BookRepository extends EntityRepository
{
    public function findOneBy(array $criteria, array $orderBy = null): ?TestApp\Entity\Book
    {
        $result = $this->findBy($criteria, $orderBy);
        if (!$result) {
            return null;
        }

        return $result[0];
    }

    /**
     * @return TestApp\Entity\Book[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $result = [];

        $criteriaId = $criteria['id'] ?? null;
        if ($criteriaId) {
            foreach ($this->findAll() as $book) {
                if ($book->getId() === $criteriaId) {
                    $result[] = $book;
                }
            }
        }

        $criteriaSlug = $criteria['slug'] ?? null;
        if ($criteriaId) {
            foreach ($this->findAll() as $book) {
                if ($book->getSlug() === $criteriaSlug) {
                    $result[] = $book;
                }
            }
        }

        return array_unique($result);
    }

    /**
     * @return TestApp\Entity\Book[]
     */
    public function findAll(): array
    {
        return [
            new TestApp\Entity\Book(1, 'keto-cookbook-beginners-low-carb-homemade', 'Keto Cookbook For Beginners: 1000 Recipes For Quick & Easy Low-Carb Homemade Cooking'),
            new TestApp\Entity\Book(2, 'design-ideas-making-house-home', 'Home Stories: Design Ideas for Making a House a Home')
        ];
    }
}
