<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use function explode;
use function strpos;

class EndpointDataExtractor
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RestApiBundle\Services\Docs\Type\TypeReader
     */
    private $typeReader;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(
        RouterInterface $router,
        RestApiBundle\Services\Docs\Type\TypeReader $typeReader
    ) {
        $this->router = $router;
        $this->typeReader = $typeReader;
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * @return RestApiBundle\DTO\Docs\EndpointData[]
     */
    public function findItems(?string $controllerNamespacePrefix = null): array
    {
        $items = [];

        foreach ($this->router->getRouteCollection() as $route) {
            [$controllerClass, $actionName] = explode('::', $route->getDefault('_controller'));

            if ($controllerNamespacePrefix && strpos($controllerClass, $controllerNamespacePrefix) !== 0) {
                continue;
            }

            $controllerReflectionClass = RestApiBundle\Services\ReflectionClassStore::get($controllerClass);
            $reflectionMethod = $controllerReflectionClass->getMethod($actionName);

            $annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, RestApiBundle\Annotation\Docs\Endpoint::class);
            if (!$annotation instanceof RestApiBundle\Annotation\Docs\Endpoint) {
                continue;
            }

            try {
                $items[] = $this->buildRouteData($reflectionMethod, $route, $annotation);
            } catch (RestApiBundle\Exception\Docs\InvalidDefinition\InvalidDefinitionExceptionInterface $exception) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinitionException($exception, $controllerClass, $actionName);
            }
        }

        return $items;
    }

    private function buildRouteData(\ReflectionMethod $reflectionMethod, Route $route, RestApiBundle\Annotation\Docs\Endpoint $annotation): RestApiBundle\DTO\Docs\EndpointData
    {
//        $actionParameters = $this->typeReader->getActionParametersByReflectionMethod($reflectionMethod);

        $returnType = $this->typeReader->getReturnTypeByReflectionMethod($reflectionMethod);
        if (!$returnType) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException();
        }

        $routeData = new RestApiBundle\DTO\Docs\EndpointData();
        $routeData
            ->setTitle($annotation->title)
            ->setDescription($annotation->description)
            ->setTags($annotation->tags)
            ->setPath($route->getPath())
            ->setMethods($route->getMethods())
            ->setReturnType($returnType);

        return $routeData;
    }
}
