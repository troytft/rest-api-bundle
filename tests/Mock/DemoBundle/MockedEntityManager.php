<?php

namespace Tests\Mock\DemoBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Tests\Mock\DemoBundle\Entity\File;
use Tests\Mock\DemoBundle\Repository\FileRepository;

class MockedEntityManager extends EntityManager implements EntityManagerInterface
{
    public function __construct()
    {
    }

    public function getRepository($entityName)
    {
        if ($entityName === File::class) {
            return new FileRepository();
        }

        return null;
    }
}
