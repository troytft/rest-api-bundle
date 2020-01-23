<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationReader;
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
     * @var RestApiBundle\Services\Docs\TypeHintHelper
     */
    private $typeHintHelper;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(
        RouterInterface $router,
        RestApiBundle\Services\Docs\DocBlockHelper $docBlockHelper,
        RestApiBundle\Services\Docs\TypeHintHelper $typeHintHelper
    ) {
        $this->router = $router;
        $this->docBlockHelper = $docBlockHelper;
        $this->typeHintHelper = $typeHintHelper;
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

            try {
                $returnTypeByDocBlock = $this->docBlockHelper->getReturnTypeByReturnTag($reflectionMethod);
                $returnTypeByTypeHint = $this->typeHintHelper->getReturnTypeByReflectionMethod($reflectionMethod);
            } catch (RestApiBundle\Exception\Docs\InvalidDefinition\InvalidDefinitionExceptionInterface $exception) {
                throw new RestApiBundle\Exception\Docs\InvalidEndpointException($exception->getMessage(), $controllerClass, $actionName);
            }

            if ($returnTypeByDocBlock) {
                $routeData->setReturnType($returnTypeByDocBlock);
            } elseif ($returnTypeByTypeHint) {
                $routeData->setReturnType($returnTypeByTypeHint);
            } else {
                throw new RestApiBundle\Exception\Docs\InvalidEndpointException('Return type not found in docBlock and type-hint.', $controllerClass, $actionName);
            }

            $items[] = $routeData;
        }

        return $items;
    }
}
