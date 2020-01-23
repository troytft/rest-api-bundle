<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationReader;
use RestApiBundle;
use Symfony\Component\Routing\RouterInterface;
use function explode;
use function sprintf;

class RouteDataExtractor
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RestApiBundle\Services\Docs\TypeReader
     */
    private $typeReader;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(
        RouterInterface $router,
        RestApiBundle\Services\Docs\TypeReader $typeReader
    ) {
        $this->router = $router;
        $this->typeReader = $typeReader;
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * @return RestApiBundle\DTO\Docs\RouteData[]
     */
    public function getItems(): array
    {
        $items = [];

        foreach ($this->router->getRouteCollection() as $route) {
            [$controllerClass, $actionName] = explode('::', $route->getDefault('_controller'));

            $controllerReflectionClass = RestApiBundle\Services\ReflectionClassStore::get($controllerClass);
            $reflectionMethod = $controllerReflectionClass->getMethod($actionName);

            $annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, RestApiBundle\Annotation\Docs\Endpoint::class);
            if (!$annotation instanceof RestApiBundle\Annotation\Docs\Endpoint) {
                continue;
            }

            $routeData = new RestApiBundle\DTO\Docs\RouteData();
            $routeData
                ->setTitle($annotation->title)
                ->setDescription($annotation->description)
                ->setTags($annotation->tags)
                ->setPath($route->getPath())
                ->setMethods($route->getMethods());

            foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                if (!isset($route->getRequirements()[$reflectionParameter->getName()])) {
                    continue;
                }

                $type = new RestApiBundle\DTO\Docs\Type\StringType($reflectionParameter->allowsNull());
                $pathParameterDescription = sprintf('String regex format is "%s".', $route->getRequirement($reflectionParameter->getName()));

                $routeData->addPathParameter(new RestApiBundle\DTO\Docs\PathParameter($reflectionParameter->getName(), $type, $pathParameterDescription));
            }

            try {
                $returnType = $this->typeReader->getReturnTypeByReflectionMethod($reflectionMethod);
            } catch (RestApiBundle\Exception\Docs\InvalidDefinition\InvalidDefinitionExceptionInterface $exception) {
                throw new RestApiBundle\Exception\Docs\InvalidEndpointException($exception->getMessage(), $controllerClass, $actionName);
            }

            if ($returnType) {
                $routeData->setReturnType($returnType);
            } else {
                throw new RestApiBundle\Exception\Docs\InvalidEndpointException('Return type not found in docBlock and type-hint.', $controllerClass, $actionName);
            }

            $items[] = $routeData;
        }

        return $items;
    }
}
