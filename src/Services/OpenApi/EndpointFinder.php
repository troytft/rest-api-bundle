<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;

use function array_merge;
use function array_slice;
use function count;
use function explode;
use function implode;
use function is_array;
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
        $endpoints = [];

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

            $endpoints[] = $this->extractEndpointsByReflectionClass(RestApiBundle\Helper\ReflectionClassStore::get($class));
        }

        return array_merge(...$endpoints);
    }

    /**
     * @return RestApiBundle\Model\OpenApi\EndpointData[]
     */
    private function extractEndpointsByReflectionClass(\ReflectionClass $reflectionClass): array
    {
        $classRouteMapping = RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionClass, Route::class);

        $endpoints = [];

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $methodRouteMapping = RestApiBundle\Helper\AnnotationReader::getMethodAnnotation($reflectionMethod, Route::class);
            if (!$methodRouteMapping instanceof Route) {
                continue;
            }

            $endpointMapping = RestApiBundle\Helper\AnnotationReader::getMethodAnnotation($reflectionMethod, RestApiBundle\Mapping\OpenApi\Endpoint::class);
            if (!$endpointMapping instanceof RestApiBundle\Mapping\OpenApi\Endpoint) {
                continue;
            }

            $endpoints[] = new RestApiBundle\Model\OpenApi\EndpointData($reflectionMethod, $endpointMapping, $methodRouteMapping, $classRouteMapping);
        }

        return $endpoints;
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
