<?php

namespace Tests;

use RestApiBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Tests;
use function print_r;
use function var_dump;
use function var_export;

class ContainerTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return RestApiBundle\RestApiBundle::class;
    }

    public function testHasRegisteredServices()
    {
        $this->bootKernel();

        $container = $this->getContainer();
        $this->assertTrue($container->has(RestApiBundle\Manager\RequestModelActionArgumentValueResolver::class));
        $this->assertTrue($container->has(RestApiBundle\Manager\RequestModelManager::class));
    }

    public function testTranslations()
    {
        $this->bootKernel();
        $container = $this->getContainer();

        /** @var RestApiBundle\Manager\RequestModelManager $requestModelManager */
        $requestModelManager = $container->get(RestApiBundle\Manager\RequestModelManager::class);

        $model = new Tests\Demo\RequestModel\CreateMovie();

        try {
            $requestModelManager->handleRequest($model, [
                'name' => 1
            ]);

            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $expectedErrors = [
                'name' => ['This value should be string.'],
            ];

            $this->assertSame($expectedErrors, $exception->getProperties());
        }
    }
}
