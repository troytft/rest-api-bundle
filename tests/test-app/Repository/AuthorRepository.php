<?php

namespace TestApp\Repository;

use Doctrine\ORM\EntityRepository;
use TestApp\Entity\Author;

use function in_array;

class AuthorRepository extends EntityRepository
{
    /**
     * @var int[]
     */
    private $existIds = [1, 2,];

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        if (isset($criteria['id']) && in_array($criteria['id'], $this->existIds)) {
            return $this->createEntityWithId($criteria['id']);
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


        return $result;
    }

    private function createEntityWithId(int $id): Author
    {
        $genre = new Author();
        $genre
            ->setId($id);

        return $genre;
    }
}
