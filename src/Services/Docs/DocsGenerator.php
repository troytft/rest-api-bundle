<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationReader;
use RestApiBundle;
use Symfony\Component\Routing\RouteCollection;
use phpDocumentor\Reflection\DocBlockFactory;
use function explode;
use function rtrim;
use function var_dump;

class DocsGenerator
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var \ReflectionClass[]
     */
    private $reflectionClassCache;

    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function generate(RouteCollection $routeCollection)
    {
        foreach ($routeCollection as $route) {
            [$controllerClass, $actionName] = explode('::', $route->getDefault('_controller'));

            $controllerReflectionClass = $this->getReflectionByClass($controllerClass);
            $actionReflectionMethod = $controllerReflectionClass->getMethod($actionName);

            $annotation = $this->annotationReader->getMethodAnnotation($actionReflectionMethod, RestApiBundle\Annotation\Docs\Endpoint::class);
            if (!$annotation instanceof RestApiBundle\Annotation\Docs\Endpoint) {
                continue;
            }

            $endpointData = new RestApiBundle\DTO\Docs\EndpointData();
            $endpointData
                ->setUrl($route->getPath())
                ->setMethods($route->getMethods())
                ->setTitle($annotation->title)
                ->setDescription($annotation->description)
                ->setTags($annotation->tags);

            var_dump($annotation);
            $docBlock = $this->docBlockFactory->create($actionReflectionMethod->getDocComment());

            var_dump($docBlock->getTagsByName('param'), $docBlock->getTagsByName('return'));
            //var_dump($annotation);
            var_dump($actionReflectionMethod->getReturnType(), $actionReflectionMethod->getParameters());
            //var_dump($route->getMethods(), $route->getPath(), $route->getDefault('_controller'), $controllerClass, $actionName);
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
