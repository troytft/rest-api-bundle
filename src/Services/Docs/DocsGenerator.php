<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use Symfony\Component\Routing\RouterInterface;
use function explode;
use function rtrim;
use function var_dump;

class DocsGenerator
{
    /**
     * @var \ReflectionClass[]
     */
    private $reflectionClassCache;

    public function generate(RouterInterface $router)
    {
        $routeCollection = $router->getRouteCollection();

        foreach ($routeCollection as $route) {
            [$controllerClass, $actionName] = explode('::', $route->getDefault('_controller'));

            $controllerReflectionClass = $this->getReflectionByClass($controllerClass);
            $actionReflectionMethod = $controllerReflectionClass->getMethod($actionName);

            var_dump($actionReflectionMethod->getDocComment(), $actionReflectionMethod->getModifiers(), $actionReflectionMethod->getReturnType(), $actionReflectionMethod->getParameters());
            var_dump($route->getMethods(), $route->getPath(), $route->getDefault('_controller'), $controllerClass, $actionName);
        }
    }

    private function getReflectionByClass(string $class): \ReflectionClass
    {
        $class = rtrim($class, '\\');

        if (!isset($this->reflectionClassCache[$class])) {
            $this->reflectionClassCache[$class] = new \ReflectionClass($class);
        }

        return $this->reflectionClassCache[$class];
    }
}
