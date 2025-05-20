<?php

declare(strict_types=1);

namespace RestApiBundle\Services\OpenApi;

use Composer\Autoload\ClassLoader;
use RestApiBundle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;

class EndpointFinder
{
    /**
     * @param string[] $excludePaths
     *
     * @return RestApiBundle\Model\OpenApi\EndpointData[]
     */
    public function findInDirectory(string $directory, array $excludePaths = []): array
    {
        $endpoints = [];

        $finder = new Finder();
        $finder
            ->files()
            ->in($directory)
            ->name('*Controller.php')
            ->notPath($excludePaths);

        $autoloadFixed = false;

        foreach ($finder as $fileInfo) {
            $class = RestApiBundle\Helper\PhpFileParserHelper::getClassByFileInfo($fileInfo);
            if (!$class) {
                continue;
            }

            if (!$autoloadFixed) {
                $filePathParts = explode('/', $fileInfo->getPathname());
                $namespaceDirectory = implode('/', \array_slice($filePathParts, 0, \count($filePathParts) - substr_count($class, '\\') - 1));
                $this->getClassLoader()->add('', $namespaceDirectory);

                $autoloadFixed = true;
            }

            $endpoints[] = $this->extractEndpointsByReflectionClass(RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class));
        }

        return array_merge(...$endpoints);
    }

    /**
     * @return RestApiBundle\Model\OpenApi\EndpointData[]
     */
    private function extractEndpointsByReflectionClass(\ReflectionClass $reflectionClass): array
    {
        $result = [];
        $classRouteMapping = RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionClass, Route::class);

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $methodRouteMapping = RestApiBundle\Helper\AnnotationReader::getMethodAnnotation($reflectionMethod, Route::class);
            if (!$methodRouteMapping) {
                continue;
            }

            $endpointMapping = RestApiBundle\Helper\AnnotationReader::getMethodAnnotation($reflectionMethod, RestApiBundle\Mapping\OpenApi\Endpoint::class);
            if (!$endpointMapping) {
                continue;
            }

            $result[] = new RestApiBundle\Model\OpenApi\EndpointData(
                $reflectionMethod,
                $endpointMapping,
                $methodRouteMapping,
                $classRouteMapping,
                RestApiBundle\Helper\ReflectionHelper::isDeprecated($reflectionMethod),
            );
        }

        return $result;
    }

    private function getClassLoader(): ClassLoader
    {
        $result = null;
        foreach (spl_autoload_functions() as $classWithFunction) {
            if (!\is_array($classWithFunction)) {
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
