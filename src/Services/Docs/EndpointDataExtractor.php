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
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader
     */
    private $docBlockSchemaReader;

    /**
     * @var RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader
     */
    private $typeHintSchemaReader;

    /**
     * @var RestApiBundle\Services\Docs\Schema\ResponseModelSchemaReader
     */
    private $responseModelSchemaReader;

    public function __construct(
        RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader $docBlockSchemaReader,
        RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader $typeHintSchemaReader,
        RestApiBundle\Services\Docs\Schema\ResponseModelSchemaReader $responseModelSchemaReader
    ) {
        $this->annotationReader = new AnnotationReader();
        $this->docBlockSchemaReader = $docBlockSchemaReader;
        $this->typeHintSchemaReader = $typeHintSchemaReader;
        $this->responseModelSchemaReader = $responseModelSchemaReader;
    }

    public function extractFromRoute(Route $route): ?RestApiBundle\DTO\Docs\EndpointData
    {
        [$controllerClass, $actionName] = explode('::', $route->getDefault('_controller'));

        $reflectionController = RestApiBundle\Services\ReflectionClassStore::get($controllerClass);
        $reflectionMethod = $reflectionController->getMethod($actionName);

        $annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, RestApiBundle\Annotation\Docs\Endpoint::class);
        if (!$annotation instanceof RestApiBundle\Annotation\Docs\Endpoint) {
            return null;
        }

        try {
            $result = $this->buildEndpointData($route, $annotation, $reflectionMethod);
        } catch (RestApiBundle\Exception\Docs\InvalidDefinition\InvalidDefinitionExceptionInterface $exception) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinitionException($exception, $controllerClass, $actionName);
        }

        return $result;
    }

    private function buildEndpointData(Route $route, RestApiBundle\Annotation\Docs\Endpoint $annotation, \ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\EndpointData
    {
        $routePathParameterNames = $this->parseRoutePathParameterNames($route->getPath());
        if (array_diff(array_keys($route->getRequirements()), $routePathParameterNames)) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException();
        }

        $methodParameters = $this->getMethodParameters($reflectionMethod);
        $pathParameters = $this->getPathParametersFromMethodParameters($methodParameters);

        $responseSchema = $this->getMethodReturnSchema($reflectionMethod);
        if (!$responseSchema) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException();
        }

        $endpointData = new RestApiBundle\DTO\Docs\EndpointData();
        $endpointData
            ->setTitle($annotation->title)
            ->setDescription($annotation->description)
            ->setTags($annotation->tags)
            ->setPath($route->getPath())
            ->setMethods($route->getMethods())
            ->setResponseSchema($responseSchema)
            ->setPathParameters($pathParameters);

        return $endpointData;
    }

    /**
     * @param RestApiBundle\DTO\Docs\SchemaWithName[] $methodParameters
     *
     * @return RestApiBundle\DTO\Docs\PathParameter[]
     */
    private function getPathParametersFromMethodParameters(array $methodParameters): array
    {
        $result = [];

        foreach ($methodParameters as $methodParameter) {
            if (!$methodParameter instanceof RestApiBundle\DTO\Docs\SchemaWithName) {
                throw new \InvalidArgumentException();
            }

            $parameterInnerType = $methodParameter->getSchema();
            if ($parameterInnerType instanceof RestApiBundle\DTO\Docs\Schema\ScalarInterface) {
                $result[] = new RestApiBundle\DTO\Docs\PathParameter($methodParameter->getName(), $methodParameter->getSchema());
            }
        }

        return $result;
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

    private function getMethodReturnSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $type = $this->docBlockSchemaReader->getMethodReturnSchema($reflectionMethod) ?: $this->typeHintSchemaReader->getMethodReturnSchema($reflectionMethod);

        if ($type instanceof RestApiBundle\DTO\Docs\Schema\ClassType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($type->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $type = $this->responseModelSchemaReader->getSchemaByClass($type->getClass(), $type->getNullable());
        } elseif ($type instanceof RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($type->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $objectType = $this->responseModelSchemaReader->getSchemaByClass($type->getClass(), $type->getNullable());
            $type = new RestApiBundle\DTO\Docs\Schema\ArrayType($objectType, $objectType->getNullable());
        }

        return $type;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return RestApiBundle\DTO\Docs\SchemaWithName[]
     */
    private function getMethodParameters(\ReflectionMethod $reflectionMethod): array
    {
        $result = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $type = $this->typeHintSchemaReader->getMethodParameterSchema($reflectionParameter);
            $result[] = new RestApiBundle\DTO\Docs\SchemaWithName($reflectionParameter->getName(), $type);
        }

        return $result;
    }
}
