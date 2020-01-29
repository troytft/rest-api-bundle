<?php

namespace Tests\TestApp\TestBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Tests\TestApp\TestBundle\Entity\Genre;
use function in_array;

class GenreRepository extends EntityRepository
{
    /**
     * @var int[]
     */
    private $existIds = [1, 2,];

    /**
     * @var string[]
     */
    private $existSlugs = ['action',];

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        if (isset($criteria['id']) && in_array($criteria['id'], $this->existIds)) {
            return $this->createEntityWithId($criteria['id']);
        }

        if (isset($criteria['slug']) && in_array($criteria['slug'], $this->existSlugs)) {
            return $this->createEntityWithSlug($criteria['slug']);
        }

        return null;
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $result = [];

        if (isset($criteria['id'])) {
            foreach ($criteria['id'] as $id) {
                if (in_array($id, $this->existIds)) {
                    $result[] = $this->createEntityWithId($id);
                }
            }
        }

        if (isset($criteria['slug'])) {
            foreach ($criteria['slug'] as $slug) {
                if (in_array($slug, $this->existSlugs)) {
                    $result[] = $this->createEntityWithSlug($slug);
                }
            }
        }

        return $result;
    }

    private function createEntityWithId(int $id): Genre
    {
        $genre = new Genre();
        $genre
            ->setId($id);

        return $genre;
    }

    private function createEntityWithSlug(string $slug): Genre
    {
        $genre = new Genre();
        $genre
            ->setSlug($slug);

        return $genre;
    }
}
