<?php

namespace Tests\Mock\DemoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Tests\Mock\DemoBundle\Entity\File;

class FileRepository extends EntityRepository
{
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
