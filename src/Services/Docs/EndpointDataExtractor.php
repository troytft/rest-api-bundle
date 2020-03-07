<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationReader;
use RestApiBundle;
use Symfony\Component\Routing\Route;
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
            $result = $this->extractData($route, $annotation, $reflectionMethod);
        } catch (RestApiBundle\Exception\Docs\InvalidDefinition\InvalidDefinitionExceptionInterface $exception) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinitionException($exception, $controllerClass, $actionName);
        }

        return $result;
    }

    private function extractData(Route $route, RestApiBundle\Annotation\Docs\Endpoint $annotation, \ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\EndpointData
    {
        $this->assertPathParametersMatchRouteRequirements($route);

        $methodParameters = $this->getMethodParameters($reflectionMethod);
        $pathParameters = $this->getPathParameters($methodParameters);

        $responseSchema = $this->getResponseSchema($reflectionMethod);
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
    private function getPathParameters(array $methodParameters): array
    {
        $result = [];

        foreach ($methodParameters as $methodParameter) {
            if (!$methodParameter instanceof RestApiBundle\DTO\Docs\SchemaWithName) {
                throw new \InvalidArgumentException();
            }

            $schema = $methodParameter->getSchema();
            if ($schema instanceof RestApiBundle\DTO\Docs\Schema\ScalarInterface) {
                if (!$schema instanceof RestApiBundle\DTO\Docs\Schema\IntegerType && !$schema instanceof RestApiBundle\DTO\Docs\Schema\StringType) {
                    throw new RestApiBundle\Exception\Docs\InvalidDefinition\NotAllowedFunctionParameterTypeException();
                }

                $result[] = new RestApiBundle\DTO\Docs\PathParameter($methodParameter->getName(), $methodParameter->getSchema());
            }
        }

        return $result;
    }

    private function assertPathParametersMatchRouteRequirements(Route $route): void
    {
        $matches = null;
        $parameters = [];

        if (preg_match_all('/{([^}]+)}/', $route->getPath(), $matches)) {
            $parameters = $matches[1];
        }

        if (array_keys($route->getRequirements()) !== $parameters) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\PathParametersNotMatchRouteRequirementsException();
        }
    }

    private function getResponseSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $schema = $this->docBlockSchemaReader->getMethodReturnSchema($reflectionMethod) ?: $this->typeHintSchemaReader->getMethodReturnSchema($reflectionMethod);

        if ($schema instanceof RestApiBundle\DTO\Docs\Schema\ClassType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($schema->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $schema = $this->responseModelSchemaReader->getSchemaByClass($schema->getClass(), $schema->getNullable());
        } elseif ($schema instanceof RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($schema->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $responseModelSchema = $this->responseModelSchemaReader->getSchemaByClass($schema->getClass(), $schema->getNullable());
            $schema = new RestApiBundle\DTO\Docs\Schema\ArrayType($responseModelSchema, $schema->getNullable());
        }

        return $schema;
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
