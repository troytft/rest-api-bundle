<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use RestApiBundle;
use Symfony\Component\Routing\Route;
use function explode;
use function preg_match_all;
use function sprintf;
use function var_dump;

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

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader $docBlockSchemaReader,
        RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader $typeHintSchemaReader,
        RestApiBundle\Services\Docs\Schema\ResponseModelSchemaReader $responseModelSchemaReader,
        EntityManagerInterface $entityManager
    ) {
        $this->annotationReader = new AnnotationReader();
        $this->docBlockSchemaReader = $docBlockSchemaReader;
        $this->typeHintSchemaReader = $typeHintSchemaReader;
        $this->responseModelSchemaReader = $responseModelSchemaReader;
        $this->entityManager = $entityManager;
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
            $endpointData = new RestApiBundle\DTO\Docs\EndpointData();
            $endpointData
                ->setTitle($annotation->title)
                ->setDescription($annotation->description)
                ->setTags($annotation->tags)
                ->setPath($route->getPath())
                ->setMethods($route->getMethods())
                ->setResponseSchema($this->getResponseSchema($reflectionMethod))
                ->setPathParameters($this->getPathParameters($route, $reflectionMethod));
        } catch (RestApiBundle\Exception\Docs\InvalidDefinition\InvalidDefinitionExceptionInterface $exception) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinitionException($exception, $controllerClass, $actionName);
        }

        return $endpointData;
    }

    /**
     * @return RestApiBundle\DTO\Docs\PathParameter[]
     */
    private function getPathParameters(Route $route, \ReflectionMethod $reflectionMethod): array
    {
        $result = [];
        $parameterIndex = 0;
        $placeholders = $this->getRoutePathPlaceholders($route);

        foreach ($placeholders as $placeholder) {
            $associatedParameter = null;

            while (true) {
                if (!isset($reflectionMethod->getParameters()[$parameterIndex])) {
                    break;
                }

                $associatedParameter = $reflectionMethod->getParameters()[$parameterIndex];
                $parameterIndex++;

                if (!$associatedParameter->hasType()) {
                    continue;
                }

                if ($associatedParameter->getName() === $placeholder || $this->isDoctrineEntity((string) $associatedParameter->getType())) {
                    break;
                }
            }

            if (!$associatedParameter) {
                throw new \InvalidArgumentException(sprintf('Associated parameter for placeholder %s not found.', $placeholder));
            }

            $result[] = new RestApiBundle\DTO\Docs\PathParameter($placeholder, $this->typeHintSchemaReader->getMethodParameterSchema($associatedParameter));
        }

        return $result;
    }

    private function isDoctrineEntity(string $className): bool
    {
        return !$this->entityManager->getMetadataFactory()->isTransient($className);
    }

    private function getRoutePathPlaceholders(Route $route): array
    {
        $matches = null;
        $parameters = [];

        if (preg_match_all('/{([^}]+)}/', $route->getPath(), $matches)) {
            $parameters = $matches[1];
        }

        return $parameters;
    }

    private function getResponseSchema(\ReflectionMethod $reflectionMethod): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
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

        if (!$schema) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyReturnTypeException();
        }

        return $schema;
    }
}
