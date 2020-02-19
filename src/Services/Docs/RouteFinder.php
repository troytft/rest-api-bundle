<?php

namespace RestApiBundle\Services\Docs;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use function strpos;

class RouteFinder
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string|null $namespaceFilter
     *
     * @return Route[]
     */
    public function find(?string $namespaceFilter = null): array
    {
        $result = [];

        foreach ($this->router->getRouteCollection() as $route) {
            if (!$namespaceFilter || strpos($route->getDefault('_controller'), $namespaceFilter) !== 0) {
                continue;
            }

            $result[] = $route;
        }

        return $result;
    }
}
