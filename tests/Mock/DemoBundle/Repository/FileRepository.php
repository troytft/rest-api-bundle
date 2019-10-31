<?php

namespace Tests\Mock\DemoBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Tests\Mock\DemoBundle\Entity\File;
use function var_dump;

class FileRepository extends EntityRepository
{
    public function __construct()
    {
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        if (isset($criteria['id']) && $criteria['id'] === 1) {
            return $this->createEntityWithId(1);
        }

        return null;
    }

    private function createEntityWithId(int $id): File
    {
        $file = new File();
        $file
            ->setId($id);

        return $file;
    }
}
