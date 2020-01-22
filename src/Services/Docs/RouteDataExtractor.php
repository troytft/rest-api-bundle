<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationReader;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Object_;
use RestApiBundle;
use Symfony\Component\Routing\RouterInterface;
use function explode;

class RouteDataExtractor
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RestApiBundle\Services\Docs\DocBlockHelper
     */
    private $docBlockHelper;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(RouterInterface $router, RestApiBundle\Services\Docs\DocBlockHelper $docBlockHelper)
    {
        $this->router = $router;
        $this->docBlockHelper = $docBlockHelper;
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

            $controllerReflectionClass = new \ReflectionClass($controllerClass);
            $actionReflectionMethod = $controllerReflectionClass->getMethod($actionName);

            $annotation = $this->annotationReader->getMethodAnnotation($actionReflectionMethod, RestApiBundle\Annotation\Docs\Endpoint::class);
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

            $returnTypeByDocBlock = $this->docBlockHelper->getReturnTypeByReturnTag($actionReflectionMethod);
            if ($returnTypeByDocBlock) {
                $routeData->setReturnType($returnTypeByDocBlock);
            } elseif ($actionReflectionMethod->getReturnType()) {
                if ($actionReflectionMethod->getReturnType()->allowsNull()) {
                    throw new \InvalidArgumentException('Not implemented.');
                }

                $responseClass = (string) $actionReflectionMethod->getReturnType();
                $routeData->setReturnType(new RestApiBundle\DTO\Docs\ReturnType\ClassType($responseClass, false));
            } else {
                throw new RestApiBundle\Exception\Docs\InvalidEndpointException('Return type not specified.', $controllerClass, $actionName);
            }

            $items[] = $routeData;
        }

        return $items;
    }


}
