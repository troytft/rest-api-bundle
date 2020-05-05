<?php

namespace RestApiBundle\Services\Docs;

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
use function preg_match_all;
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
     * @var RestApiBundle\Services\Docs\Schema\DocBlockReader
     */
    private $docBlockSchemaReader;

    /**
     * @var RestApiBundle\Services\Docs\Schema\TypeHintReader
     */
    private $typeHintSchemaReader;

    /**
     * @var RestApiBundle\Services\Docs\ResponseCollector
     */
    private $responseCollector;

    /**
     * @var RestApiBundle\Services\Docs\DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var RestApiBundle\Services\Docs\RequestModelHelper
     */
    private $requestModelHelper;

    public function __construct(
        RestApiBundle\Services\Docs\Schema\DocBlockReader $docBlockSchemaReader,
        RestApiBundle\Services\Docs\Schema\TypeHintReader $typeHintSchemaReader,
        RestApiBundle\Services\Docs\ResponseCollector $responseCollector,
        RestApiBundle\Services\Docs\DoctrineHelper $doctrineHelper,
        RestApiBundle\Services\Docs\RequestModelHelper $requestModelHelper
    ) {
        $this->annotationReader = AnnotationReaderFactory::create(true);
        $this->docBlockSchemaReader = $docBlockSchemaReader;
        $this->typeHintSchemaReader = $typeHintSchemaReader;
        $this->responseCollector = $responseCollector;
        $this->doctrineHelper = $doctrineHelper;
        $this->requestModelHelper = $requestModelHelper;
    }

    /**
     * @return RestApiBundle\DTO\Docs\EndpointData[]
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
     * @return RestApiBundle\DTO\Docs\EndpointData[]
     */
    private function extractFromController(string $class): array
    {
        $result = [];

        $reflectionController = RestApiBundle\Services\ReflectionClassStore::get($class);
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

            $requestModelSchema = null;
            $requestModelClass = $this->getRequestModel($reflectionMethod);
            if ($requestModelClass) {
                $requestModelSchema = $this->requestModelHelper->getSchemaByClass($requestModelClass);
            }

            try {
                if ($controllerRouteAnnotation instanceof Route && $controllerRouteAnnotation->getPath()) {
                    $path = $controllerRouteAnnotation->getPath();

                    if ($actionRouteAnnotation->getPath()) {
                        $path .= $actionRouteAnnotation->getPath();
                    }
                } elseif ($actionRouteAnnotation->getPath()) {
                    $path = $actionRouteAnnotation->getPath();
                } else {
                    throw new RestApiBundle\Exception\Docs\InvalidDefinition\EmptyRoutePathException();
                }

                $endpointData = new RestApiBundle\DTO\Docs\EndpointData();
                $endpointData
                    ->setTitle($endpointAnnotation->title)
                    ->setDescription($endpointAnnotation->description)
                    ->setTags($endpointAnnotation->tags)
                    ->setPath($path)
                    ->setMethods($actionRouteAnnotation->getMethods())
                    ->setResponse($this->responseCollector->getByReflectionMethod($reflectionMethod))
                    ->setPathParameters($this->getPathParameters($path, $reflectionMethod))
                    ->setRequestModel($requestModelSchema);

                $result[] = $endpointData;
            } catch (RestApiBundle\Exception\Docs\InvalidDefinition\BaseInvalidDefinitionException $exception) {
                $context = sprintf('%s::%s', $class, $reflectionMethod->getName());
                throw new RestApiBundle\Exception\Docs\InvalidDefinitionException($exception, $context);
            }
        }

        return $result;
    }

    /**
     * @return RestApiBundle\DTO\Docs\PathParameter[]
     */
    private function getPathParameters(string $path, \ReflectionMethod $reflectionMethod): array
    {
        $result = [];
        $parameterIndex = 0;
        $placeholders = $this->getPathPlaceholders($path);

        foreach ($placeholders as $placeholder) {
            $pathParameter = null;

            while (true) {
                if (!isset($reflectionMethod->getParameters()[$parameterIndex])) {
                    break;
                }

                $parameter = $reflectionMethod->getParameters()[$parameterIndex];
                $parameterIndex++;

                $parameterSchema = $this->typeHintSchemaReader->getMethodParameterSchema($parameter);
                if (!$parameterSchema) {
                    continue;
                }

                $isNameEqualsToPlaceholder = $parameter->getName() === $placeholder;

                if ($isNameEqualsToPlaceholder && $parameterSchema instanceof RestApiBundle\DTO\Docs\Schema\ScalarInterface) {
                    $pathParameter = new RestApiBundle\DTO\Docs\PathParameter($placeholder, $parameterSchema);

                    break;
                } elseif ($parameterSchema instanceof RestApiBundle\DTO\Docs\Schema\ClassType && $this->doctrineHelper->isEntity($parameterSchema->getClass())) {
                    $fieldName = $isNameEqualsToPlaceholder ? 'id' : $placeholder;
                    $parameterSchema = $this->doctrineHelper->getEntityFieldSchema($parameterSchema->getClass(), $fieldName, false);
                    $pathParameter = new RestApiBundle\DTO\Docs\PathParameter($placeholder, $parameterSchema);

                    break;
                }
            }

            if (!$pathParameter) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\NotMatchedRoutePlaceholderParameterException($placeholder);
            }

            $result[] = $pathParameter;
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

    private function getRequestModel(\ReflectionMethod $reflectionMethod): ?string
    {
        $result = null;

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $schema = $this->typeHintSchemaReader->getMethodParameterSchema($parameter);
            if ($schema instanceof RestApiBundle\DTO\Docs\Schema\ClassType && RestApiBundle\Services\Request\RequestModelHelper::isRequestModel($schema->getClass())) {
                $result = $schema->getClass();

                break;
            }
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
