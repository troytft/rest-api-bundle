<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationReader;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\Object_;
use RestApiBundle;
use Symfony\Component\Routing\RouteCollection;
use phpDocumentor\Reflection\DocBlockFactory;
use function count;
use function explode;
use function ltrim;
use function reset;
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
            } elseif ($actionReflectionMethod->getReturnType()) {
                if ($actionReflectionMethod->getReturnType()->allowsNull()) {
                    throw new \InvalidArgumentException('Not implemented.');
                }

                $responseClass = (string) $actionReflectionMethod->getReturnType();
            } else {
                throw new RestApiBundle\Exception\Docs\InvalidEndpointException('Return type not specified.', $controllerClass, $actionName);
            }

            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($responseClass)) {
                throw new \InvalidArgumentException('Not implemented');
            }

            var_dump($endpointData);
            //var_dump($annotation);
//            var_dump(, $actionReflectionMethod->getParameters());
            //var_dump($route->getMethods(), $route->getPath(), $route->getDefault('_controller'), $controllerClass, $actionName);
        }
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

    private function getResponseScheme(string $class)
    {

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
