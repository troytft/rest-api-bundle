<?php

namespace RestApiBundle\Services\OpenApi;

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Mapper\Helper\AnnotationReaderFactory;
use RestApiBundle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\Annotation\Route;
use function array_merge;
use function array_slice;
use function count;
use function explode;
use function implode;
use function is_array;
use function is_string;
use function spl_autoload_functions;
use function sprintf;
use function substr_count;
use function token_get_all;

class EndpointFinder
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var RestApiBundle\Services\OpenApi\Reader\DocBlockReader
     */
    private $docBlockReader;

    /**
     * @var RestApiBundle\Services\OpenApi\Reader\TypeHintReader
     */
    private $typeHintReader;

    public function __construct(
        RestApiBundle\Services\OpenApi\Reader\DocBlockReader $docBlockReader,
        RestApiBundle\Services\OpenApi\Reader\TypeHintReader $typeHintReader
    ) {
        $this->annotationReader = AnnotationReaderFactory::create(true);
        $this->docBlockReader = $docBlockReader;
        $this->typeHintReader = $typeHintReader;
    }

    /**
     * @return RestApiBundle\DTO\OpenApi\EndpointData[]
     */
    public function findInDirectory(string $directory): array
    {
        $result = [];

        $finder = new Finder();
        $finder
            ->files()
            ->in($directory)
            ->name('*Controller.php');

        $autoloadFixed = false;

        foreach ($finder as $fileInfo) {
            $class = $this->getClassByFileInfo($fileInfo);

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

    private function getClassByFileInfo(SplFileInfo $fileInfo): string
    {
        $tokens = token_get_all($fileInfo->getContents());

        $namespaceTokenOpened = false;
        $namespace = '';

        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === \T_NAMESPACE) {
                $namespaceTokenOpened = true;
            } elseif ($namespaceTokenOpened && is_array($token) && $token[0] !== \T_WHITESPACE) {
                $namespace .= $token[1];
            } elseif ($namespaceTokenOpened && is_string($token) && $token === ';') {
                break;
            }
        }

        if (!$namespace) {
            throw new \LogicException();
        }

        $fileNameWithoutExtension = $fileInfo->getBasename('.' . $fileInfo->getExtension());

        return sprintf('%s\%s', $namespace, $fileNameWithoutExtension);
    }

    /**
     * @return RestApiBundle\DTO\OpenApi\EndpointData[]
     */
    private function extractFromController(string $class): array
    {
        $result = [];

        $reflectionController = RestApiBundle\Services\ReflectionClassStore::get($class);
        /** @var Route|null $controllerRouteAnnotation */
        $controllerRouteAnnotation = $this->annotationReader->getClassAnnotation($reflectionController, Route::class);

        foreach ($reflectionController->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $actionRouteAnnotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, Route::class);
            if (!$actionRouteAnnotation instanceof Route) {
                continue;
            }

            $endpointAnnotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, RestApiBundle\Annotation\Docs\Endpoint::class);
            if (!$endpointAnnotation instanceof RestApiBundle\Annotation\Docs\Endpoint) {
                continue;
            }

            try {
                $returnType = $this->docBlockReader->getMethodReturnSchema($reflectionMethod) ?: $this->typeHintReader->getMethodReturnSchema($reflectionMethod);
                if (!$returnType) {
                    throw new RestApiBundle\Exception\OpenApi\InvalidDefinition\EmptyReturnTypeException();
                }

                $endpointData = new RestApiBundle\DTO\OpenApi\EndpointData();
                $endpointData
                    ->setEndpointAnnotation($endpointAnnotation)
                    ->setControllerRouteAnnotation($controllerRouteAnnotation)
                    ->setActionRouteAnnotation($actionRouteAnnotation)
                    ->setReturnType($returnType)
                    ->setParameters($this->getActionParameters($reflectionMethod));

                $result[] = $endpointData;
            } catch (RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException $exception) {
                $context = sprintf('%s::%s', $class, $reflectionMethod->getName());
                throw new RestApiBundle\Exception\OpenApi\InvalidDefinitionException($exception, $context);
            }
        }

        return $result;
    }

    /**
     * @return RestApiBundle\DTO\OpenApi\Schema\TypeInterface[]
     */
    private function getActionParameters(\ReflectionMethod $reflectionMethod): array
    {
        $result = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $schema = $this->typeHintReader->getTypeByReflectionParameter($reflectionParameter);
            if (!$schema) {
                continue;
            }

            $result[] = $schema;
        }

        return $result;
    }

    private function getClassLoader(): ClassLoader
    {
        $result = null;
        foreach (spl_autoload_functions() as $classWithFunction) {
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
