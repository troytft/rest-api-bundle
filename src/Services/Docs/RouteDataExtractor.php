<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use function array_diff;
use function array_keys;
use function explode;
use function strpos;

class RouteDataExtractor
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
     * @return RestApiBundle\DTO\Docs\RouteData[]
     */
    public function getItems(?string $controllerNamespacePrefix = null): array
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

    /**
     * @param string $path
     *
     * @return string[]
     */
    private function parseRoutePathParameterNames(string $path): array
    {
        $matches = null;
        if (!preg_match_all('/{([^}]+)}/', $path, $matches)) {
            return [];
        }

        return $matches[1];
    }

    private function buildRouteData(\ReflectionMethod $reflectionMethod, Route $route, RestApiBundle\Annotation\Docs\Endpoint $annotation): RestApiBundle\DTO\Docs\RouteData
    {
        $routePathParameterNames = $this->parseRoutePathParameterNames($route->getPath());
        if (array_diff(array_keys($route->getRequirements()), $routePathParameterNames)) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException();
        }

//        $actionParameters = $this->typeReader->getActionParametersByReflectionMethod($reflectionMethod);

//        var_dump(array_keys($actionParameters));

        $returnType = $this->typeReader->getReturnTypeByReflectionMethod($reflectionMethod);
        if (!$returnType) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException();
        }

        $routeData = new RestApiBundle\DTO\Docs\RouteData();
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
