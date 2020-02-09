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
     * @var RestApiBundle\Services\Docs\Type\DocBlockReader
     */
    private $docBlockReader;

    /**
     * @var RestApiBundle\Services\Docs\Type\TypeHintReader
     */
    private $typeHintReader;

    /**
     * @var RestApiBundle\Services\Docs\Type\ResponseModelReader
     */
    private $responseModelReader;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(
        RouterInterface $router,
        RestApiBundle\Services\Docs\Type\DocBlockReader $docBlockReader,
        RestApiBundle\Services\Docs\Type\TypeHintReader $typeHintReader,
        RestApiBundle\Services\Docs\Type\ResponseModelReader $responseModelReader
    ) {
        $this->router = $router;
        $this->docBlockReader = $docBlockReader;
        $this->typeHintReader = $typeHintReader;
        $this->responseModelReader = $responseModelReader;
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
    private function parseRoutePathParameters(string $path): array
    {
        $matches = null;
        if (!preg_match_all('/{([^}]+)}/', $path, $matches)) {
            return [];
        }

        return $matches[1];
    }

    private function buildRouteData(\ReflectionMethod $reflectionMethod, Route $route, RestApiBundle\Annotation\Docs\Endpoint $annotation): RestApiBundle\DTO\Docs\RouteData
    {
        $routeData = new RestApiBundle\DTO\Docs\RouteData();
        $routeData
            ->setTitle($annotation->title)
            ->setDescription($annotation->description)
            ->setTags($annotation->tags)
            ->setPath($route->getPath())
            ->setMethods($route->getMethods());

        $routePathParameters = $this->parseRoutePathParameters($route->getPath());
        if (array_diff(array_keys($route->getRequirements()), $routePathParameters)) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException();
        }

//        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
//            $parameterType = $this->typeHintReader->getParameterTypeByReflectionParameter($reflectionParameter);
//            if (!$parameterType || $parameterType instanceof RestApiBundle\DTO\Docs\Type\NullType) {
//                continue;
//            }
//
//            if (isset($route->getRequirements()[$reflectionParameter->getName()])) {
//                if ($parameterType instanceof RestApiBundle\DTO\Docs\Type\ScalarInterface) {
//
//                    $routeData->addPathParameter(new RestApiBundle\DTO\Docs\RouteParameter(RestApiBundle\Enum\Docs\RouteParameterType::PATH, $reflectionParameter->getName(), $parameterType));
//
//                    continue;
//                } else {
//                    throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedParameterTypeException();
//                }
//            }
//        }

        $returnType = $this->docBlockReader->getReturnType($reflectionMethod) ?: $this->typeHintReader->getReturnType($reflectionMethod);

        if ($returnType instanceof RestApiBundle\DTO\Docs\Type\ClassType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($returnType->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $routeData->setReturnType($this->responseModelReader->getTypeByClass($returnType->getClass(), $returnType->getNullable()));
        } elseif ($returnType instanceof RestApiBundle\DTO\Docs\Type\ArrayOfClassesType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($returnType->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $objectType = $this->responseModelReader->getTypeByClass($returnType->getClass(), $returnType->getNullable());
            $routeData->setReturnType(new RestApiBundle\DTO\Docs\Type\ArrayType($objectType, $objectType->getNullable()));
        } elseif (!$returnType) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException();
        }

        return $routeData;
    }
}
