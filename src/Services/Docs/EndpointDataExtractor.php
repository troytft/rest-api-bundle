<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationReader;
use RestApiBundle;
use Symfony\Component\Routing\Route;
use function array_diff;
use function array_keys;
use function explode;
use function preg_match_all;

class EndpointDataExtractor
{
    /**
     * @var RestApiBundle\Services\Docs\Type\TypeReader
     */
    private $typeReader;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function extractFromRoute(Route $route): ?RestApiBundle\DTO\Docs\EndpointData
    {
        [$controllerClass, $actionName] = explode('::', $route->getDefault('_controller'));

        $reflectionController = RestApiBundle\Services\ReflectionClassStore::get($controllerClass);
        $reflectionMethod = $reflectionController->getMethod($actionName);

        $annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, RestApiBundle\Annotation\Docs\Endpoint::class);
        if (!$annotation instanceof RestApiBundle\Annotation\Docs\Endpoint) {
            return null;
        }

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

        $endpointData = new RestApiBundle\DTO\Docs\EndpointData();
        $endpointData
            ->setTitle($annotation->title)
            ->setDescription($annotation->description)
            ->setTags($annotation->tags)
            ->setPath($route->getPath())
            ->setMethods($route->getMethods())
            ->setReturnType($returnType);

        return $endpointData;
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
}
