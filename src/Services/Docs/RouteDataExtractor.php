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
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->annotationReader = new AnnotationReader();
        $this->docBlockFactory = DocBlockFactory::createInstance();
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

            $docBlock = $this->docBlockFactory->create($actionReflectionMethod->getDocComment());

            if ($docBlock->getTagsByName('return')) {
                if (count($docBlock->getTagsByName('return')) > 1) {
                    throw new RestApiBundle\Exception\Docs\InvalidEndpointException('DocBlock contains more then one @return tag.', $controllerClass, $actionName);
                }

                $docBlockReturnType = $docBlock->getTagsByName('return')[0];
                if (!$docBlockReturnType instanceof Return_) {
                    throw new \InvalidArgumentException();
                }

                $responseClass = $this->getResponseClassByReturnDocBlock($docBlockReturnType);
                $routeData->setReturnType(new RestApiBundle\DTO\Docs\ReturnType\ClassType($responseClass, false));
            } elseif ($actionReflectionMethod->getReturnType()) {
                if ($actionReflectionMethod->getReturnType()->allowsNull()) {
                    throw new \InvalidArgumentException('Not implemented.');
                }

                $responseClass = (string) $actionReflectionMethod->getReturnType();
                $routeData->setReturnType(new RestApiBundle\DTO\Docs\ReturnType\ClassType($responseClass, false));
            } else {
                throw new RestApiBundle\Exception\Docs\InvalidEndpointException('Return type not specified.', $controllerClass, $actionName);
            }

            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($responseClass)) {
                throw new \InvalidArgumentException('Not implemented');
            }

            $items[] = $routeData;
        }

        return $items;
    }

    private function getResponseClassByReturnDocBlock(Return_ $returnDocBlock): string
    {
        if ($returnDocBlock->getType() instanceof Object_) {
            $class = (string) $returnDocBlock->getType();
        } else {
            throw new \InvalidArgumentException('Not implemented.');
        }

        return $class;
    }
}
