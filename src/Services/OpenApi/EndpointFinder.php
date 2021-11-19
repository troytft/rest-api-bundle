<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PropertyInfo;

use function array_merge;
use function array_slice;
use function count;
use function explode;
use function implode;
use function is_array;
use function preg_match_all;
use function reset;
use function spl_autoload_functions;
use function substr_count;

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
        $controllerRoute = RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionController, Route::class);

        foreach ($reflectionController->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $actionRoute = RestApiBundle\Helper\AnnotationReader::getMethodAnnotation($reflectionMethod, Route::class);
            if (!$actionRoute instanceof Route) {
                continue;
            }

            $endpointAnnotation = RestApiBundle\Helper\AnnotationReader::getMethodAnnotation($reflectionMethod, RestApiBundle\Mapping\OpenApi\Endpoint::class);
            if (!$endpointAnnotation instanceof RestApiBundle\Mapping\OpenApi\Endpoint) {
                continue;
            }

            if ($controllerRoute instanceof Route && $controllerRoute->getPath()) {
                $path = $controllerRoute->getPath();

                if ($actionRoute->getPath()) {
                    $path .= $actionRoute->getPath();
                }
            } elseif ($actionRoute->getPath()) {
                $path = $actionRoute->getPath();
            } else {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException('Route has empty path', $class, $reflectionMethod->getName());
            }

            if (!$actionRoute->getMethods()) {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException('Route has empty methods', $class, $reflectionMethod->getName());
            }

            try {
                $endpointData = new RestApiBundle\Model\OpenApi\EndpointData();
                $endpointData
                    ->setControllerRoute($controllerRoute)
                    ->setActionRoute($actionRoute)
                    ->setReflectionMethod($reflectionMethod)
                    ->setEndpoint($endpointAnnotation)
                    ->setPath($path)
                    ->setMethods($actionRoute->getMethods())
                    ->setPathParameters($this->extractPathParameters($path, $reflectionMethod))
                    ->setRequest($this->extractRequest($reflectionMethod));

                $result[] = $endpointData;
            } catch (RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException $exception) {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException($exception->getMessage(), $class, $reflectionMethod->getName());
            }
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
