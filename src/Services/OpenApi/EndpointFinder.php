<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PropertyInfo;
use Symfony\Component\HttpFoundation;

use function array_merge;
use function array_slice;
use function count;
use function explode;
use function implode;
use function is_array;
use function is_string;
use function preg_match_all;
use function reset;
use function spl_autoload_functions;
use function substr_count;use function Symfony\Component\String\u;

class EndpointFinder
{
    /**
     * @param string[] $excludePaths
     *
     * @return RestApiBundle\Model\OpenApi\EndpointData[]
     */
    public function findInDirectory(string $directory, array $excludePaths = []): array
    {
        $result = [];

        $finder = new Finder();
        $finder
            ->files()
            ->in($directory)
            ->name('*.php')
            ->notPath($excludePaths);

        $autoloadFixed = false;

        foreach ($finder as $fileInfo) {
            $class = RestApiBundle\Helper\PhpFileParserHelper::getClassByFileInfo($fileInfo);
            if (!$class) {
                continue;
            }

            if (!$autoloadFixed) {
                $filePathParts = explode('/', $fileInfo->getPathname());
                $namespaceDirectory = implode('/', array_slice($filePathParts, 0, count($filePathParts) - substr_count($class, '\\') - 1));
                $this->getClassLoader()->add("", $namespaceDirectory);

                $autoloadFixed = true;
            }

            $result[] = $this->extractFromController($class);

        }

        return array_merge(...$result);
    }

    /**
     * @return RestApiBundle\Model\OpenApi\EndpointData[]
     */
    private function extractFromController(string $class): array
    {
        $result = [];

        $reflectionController = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $controllerRouteAnnotation = RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionController, Route::class);

        foreach ($reflectionController->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $actionRouteAnnotation = RestApiBundle\Helper\AnnotationReader::getMethodAnnotation($reflectionMethod, Route::class);
            if (!$actionRouteAnnotation instanceof Route) {
                continue;
            }

            $endpointAnnotation = RestApiBundle\Helper\AnnotationReader::getMethodAnnotation($reflectionMethod, RestApiBundle\Mapping\OpenApi\Endpoint::class);
            if (!$endpointAnnotation instanceof RestApiBundle\Mapping\OpenApi\Endpoint) {
                continue;
            }

            if ($controllerRouteAnnotation instanceof Route && $controllerRouteAnnotation->getPath()) {
                $path = $controllerRouteAnnotation->getPath();

                if ($actionRouteAnnotation->getPath()) {
                    $path .= $actionRouteAnnotation->getPath();
                }
            } elseif ($actionRouteAnnotation->getPath()) {
                $path = $actionRouteAnnotation->getPath();
            } else {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException('Route has empty path', $class, $reflectionMethod->getName());
            }

            if (!$actionRouteAnnotation->getMethods()) {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException('Route has empty methods', $class, $reflectionMethod->getName());
            }

            if (is_string($endpointAnnotation->tags)) {
                $tags = [$endpointAnnotation->tags];
            } elseif (is_array($endpointAnnotation->tags)) {
                $tags = $endpointAnnotation->tags;
            } else {
                throw new \InvalidArgumentException();
            }

            if (!$endpointAnnotation->title) {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException('Endpoint has empty title', $class, $reflectionMethod->getName());
            }

            if (!$tags) {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException('Endpoint has empty tags', $class, $reflectionMethod->getName());
            }

            try {
                $endpointData = new RestApiBundle\Model\OpenApi\EndpointData();
                $endpointData
                    ->setTitle($endpointAnnotation->title)
                    ->setDescription($endpointAnnotation->description)
                    ->setTags($tags)
                    ->setPath($path)
                    ->setMethods($actionRouteAnnotation->getMethods())
                    ->setResponse($this->extractResponse($reflectionMethod))
                    ->setPathParameters($this->extractPathParameters($path, $reflectionMethod))
                    ->setRequest($this->extractRequest($reflectionMethod));

                $result[] = $endpointData;
            } catch (RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException $exception) {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException($exception->getMessage(), $class, $reflectionMethod->getName());
            }
        }

        return $result;
    }

    private function extractResponse(\ReflectionMethod $reflectionMethod): RestApiBundle\Model\OpenApi\Response\ResponseInterface
    {
        $returnType = $this->getReturnType($reflectionMethod);

        switch (true) {
            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_NULL:
                $result = new RestApiBundle\Model\OpenApi\Response\EmptyResponse();

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($returnType->getClassName()):
                $result = new RestApiBundle\Model\OpenApi\Response\ResponseModel($returnType->getClassName(), $returnType->isNullable());

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && $returnType->getClassName() === HttpFoundation\RedirectResponse::class:
                $result = new RestApiBundle\Model\OpenApi\Response\RedirectResponse();

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && $returnType->getClassName() === HttpFoundation\BinaryFileResponse::class:
                $result = new RestApiBundle\Model\OpenApi\Response\BinaryFileResponse();

                break;

            case $returnType->isCollection() && $returnType->getCollectionValueTypes() && RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType)->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT:
                $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType);
                if (!RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($collectionValueType->getClassName())) {
                    throw new \InvalidArgumentException('Invalid response type');
                }

                $result = new RestApiBundle\Model\OpenApi\Response\ArrayOfResponseModels($collectionValueType->getClassName(), $returnType->isNullable());

                break;

            default:
                throw new \InvalidArgumentException('Invalid response type');
        }

        return $result;
    }

    private function getReturnType(\ReflectionMethod $reflectionMethod): PropertyInfo\Type
    {
        $result = RestApiBundle\Helper\TypeExtractor::extractReturnType($reflectionMethod);
        if (!$result) {
            throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException('Return type not found in docBlock and type-hint', $reflectionMethod->class, $reflectionMethod->name);
        }

        return $result;
    }

    /**
     * @return RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface[]
     */
    private function extractPathParameters(string $path, \ReflectionMethod $reflectionMethod): array
    {
        $scalarTypes = [];
        $entityTypes = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if (!$reflectionParameter->getType()) {
                continue;
            }

            $parameterType = RestApiBundle\Helper\TypeExtractor::extractByReflectionType($reflectionParameter->getType());
            if (RestApiBundle\Helper\TypeExtractor::isScalar($parameterType)) {
                $scalarTypes[$reflectionParameter->getName()] = $parameterType;
            } elseif ($parameterType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\DoctrineHelper::isEntity($parameterType->getClassName())) {
                $entityTypes[$reflectionParameter->getName()] = $parameterType;
            }
        }

        $result = [];
        $placeholders = $this->getPathPlaceholders($path);

        foreach ($placeholders as $placeholder) {
            if (isset($scalarTypes[$placeholder])) {
                $result[] = new RestApiBundle\Model\OpenApi\PathParameter\ScalarParameter($placeholder, $scalarTypes[$placeholder]);
            } elseif (isset($entityTypes[$placeholder])) {
                $result[] = new RestApiBundle\Model\OpenApi\PathParameter\EntityTypeParameter($placeholder, $entityTypes[$placeholder], 'id');
                unset($entityTypes[$placeholder]);
            } else {
                $entityType = reset($entityTypes);
                if (!$entityType instanceof PropertyInfo\Type) {
                    throw new RestApiBundle\Exception\OpenApi\InvalidDefinition\NotMatchedRoutePlaceholderParameterException($placeholder);
                }
                $result[] = new RestApiBundle\Model\OpenApi\PathParameter\EntityTypeParameter($placeholder, $entityType, $placeholder);
            }
        }

        return $result;
    }

    private function getPathPlaceholders(string $path): array
    {
        $matches = null;
        $parameters = [];

        if (preg_match_all('/{([^}]+)}/', $path, $matches)) {
            $parameters = $matches[1];
        }

        return $parameters;
    }

    private function extractRequest(\ReflectionMethod $reflectionMethod): ?RestApiBundle\Model\OpenApi\Request\RequestInterface
    {
        $result = null;

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if (!$reflectionParameter->getType()) {
                continue;
            }

            $parameterType = RestApiBundle\Helper\TypeExtractor::extractByReflectionType($reflectionParameter->getType());
            if ($parameterType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($parameterType->getClassName())) {
                $result = new RestApiBundle\Model\OpenApi\Request\RequestModel($parameterType->getClassName(), $parameterType->isNullable());

                break;
            }
        }

        return $result;
    }

    private function getClassLoader(): ClassLoader
    {
        $result = null;
        foreach (spl_autoload_functions() as $classWithFunction) {
            if (!is_array($classWithFunction)) {
                continue;
            }

            if ($classWithFunction[0] instanceof ClassLoader) {
                $result = $classWithFunction[0];

                break;
            }
        }

        if (!$result) {
            throw new \InvalidArgumentException();
        }

        return $result;
    }
}
