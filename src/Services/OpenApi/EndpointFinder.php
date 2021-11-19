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

            try {
                $endpointData = new RestApiBundle\Model\OpenApi\EndpointData();
                $endpointData->controllerRouteMapping = $controllerRoute;
                $endpointData->actionRouteMapping = $actionRoute;
                $endpointData->reflectionMethod = $reflectionMethod;
                $endpointData->endpointMapping = $endpointAnnotation;

                $result[] = $endpointData;
            } catch (RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException $exception) {
                throw new RestApiBundle\Exception\ContextAware\FunctionOfClassException($exception->getMessage(), $class, $reflectionMethod->getName());
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
