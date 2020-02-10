<?php

namespace RestApiBundle\Services\Docs\Type;

use RestApiBundle;
use Symfony\Component\Routing\Route;

class TypeReader
{
    /**
     * @var RestApiBundle\Services\Docs\Type\Adapter\DocBlockAdapter
     */
    private $docBlockAdapter;

    /**
     * @var RestApiBundle\Services\Docs\Type\Adapter\TypeHintAdapter
     */
    private $typeHintAdapter;

    /**
     * @var RestApiBundle\Services\Docs\Type\Adapter\ResponseModelAdapter
     */
    private $responseModelAdapter;

    /**
     * @var RestApiBundle\Services\Docs\Type\Adapter\RouteAdapter
     */
    private $routeAdapter;

    public function __construct(
        RestApiBundle\Services\Docs\Type\Adapter\DocBlockAdapter $docBlockAdapter,
        RestApiBundle\Services\Docs\Type\Adapter\TypeHintAdapter $typeHintAdapter,
        RestApiBundle\Services\Docs\Type\Adapter\ResponseModelAdapter $responseModelAdapter,
        RestApiBundle\Services\Docs\Type\Adapter\RouteAdapter $routeAdapter
    ) {
        $this->docBlockAdapter = $docBlockAdapter;
        $this->typeHintAdapter = $typeHintAdapter;
        $this->responseModelAdapter = $responseModelAdapter;
        $this->routeAdapter = $routeAdapter;
    }

    public function getReturnTypeByReflectionMethod(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        $type = $this->docBlockAdapter->getReturnType($reflectionMethod) ?: $this->typeHintAdapter->getReturnType($reflectionMethod);

        if ($type instanceof RestApiBundle\DTO\Docs\Type\ClassType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($type->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $type = $this->responseModelAdapter->getTypeByClass($type->getClass(), $type->getNullable());
        } elseif ($type instanceof RestApiBundle\DTO\Docs\Type\ArrayOfClassesType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($type->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $objectType = $this->responseModelAdapter->getTypeByClass($type->getClass(), $type->getNullable());
            $type = new RestApiBundle\DTO\Docs\Type\ArrayType($objectType, $objectType->getNullable());
        }

        return $type;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return RestApiBundle\DTO\Docs\ActionParameter[]
     */
    public function getActionParametersByReflectionMethod(\ReflectionMethod $reflectionMethod): array
    {
        $result = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $type = $this->typeHintAdapter->getParameterTypeByReflectionParameter($reflectionParameter);
            $result[] = new RestApiBundle\DTO\Docs\ActionParameter($reflectionParameter->getName(), $type);
        }

        return $result;
    }

    /**
     * @param Route $route
     * @param RestApiBundle\DTO\Docs\ActionParameter[] $actionParameters
     * @return RestApiBundle\DTO\Docs\RouteParameter[]
     */
    public function getEndpointParametersFromRoutePath(Route $route, array $actionParameters): array
    {
        $names = $this->routeAdapter->getValidParameterNamesFromPath($route);

        $entities = [];
        $requestModel = null;

        foreach ($actionParameters as $actionParameter) {
            if (!$actionParameter instanceof RestApiBundle\DTO\Docs\Type\ClassType) {
                continue;
            }

            $actionParameter->getClass()
        }

//        var_dump(array_keys($actionParameters));

    }
}
