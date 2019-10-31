<?php

namespace Tests;

use Tests;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use RestApiBundle;
use Tests\Mock\DemoBundle\Entity\File;
use function var_dump;

class EntityTransformersTest extends BaseBundleTestCase
{
    public function testHasRegisteredServices()
    {
            $model = new Tests\Mock\DemoBundle\RequestModel\ModelWithEntity();
            $this->getRequestModelManager()->handleRequest($model, [
                'fieldWithEntity' => 1
            ]);
    }
}
