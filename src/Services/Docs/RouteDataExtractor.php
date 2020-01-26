<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Routing\RouterInterface;
use function explode;
use function sprintf;
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
    public function getItems(?string $controllerNamespacePrefix): array
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
                $returnType = $this->docBlockReader->getReturnTypeByReturnTag($reflectionMethod);
                if (!$returnType) {
                    $returnType = $this->typeHintReader->getReturnTypeByReflectionMethod($reflectionMethod);
                }
            } catch (RestApiBundle\Exception\Docs\InvalidDefinition\InvalidDefinitionExceptionInterface $exception) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinitionException($exception->getMessage(), $controllerClass, $actionName);
            }

            if (!$returnType) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinitionException('Return type not found in docBlock and type-hint.', $controllerClass, $actionName);
            }

            if ($returnType instanceof RestApiBundle\DTO\Docs\Type\ClassType) {
                if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($returnType->getClass())) {
                    throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
                }

                $returnType = $this->responseModelReader->resolveObjectTypeByClass($returnType->getClass(), $returnType->getIsNullable());
            }

            if ($returnType instanceof RestApiBundle\DTO\Docs\Type\ClassesCollectionType) {
                if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($returnType->getClass())) {
                    throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
                }

                $objectType = $this->responseModelReader->resolveObjectTypeByClass($returnType->getClass(), $returnType->getIsNullable());
                $returnType = new RestApiBundle\DTO\Docs\Type\CollectionType($objectType, $objectType->getIsNullable());
            }

            $routeData
                ->setReturnType($returnType);

            $items[] = $routeData;
        }

        return $items;
    }
}
