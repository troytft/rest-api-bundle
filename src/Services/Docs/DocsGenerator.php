<?php

namespace RestApiBundle\Services\Docs;

use cebe\openapi\spec as OpenApi;
use Doctrine\Common\Annotations\AnnotationReader;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\Object_;
use RestApiBundle;
use Symfony\Component\Routing\RouteCollection;
use phpDocumentor\Reflection\DocBlockFactory;
use function count;
use function explode;
use function lcfirst;
use function ltrim;
use function reset;
use function rtrim;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function var_dump;

class DocsGenerator
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var \ReflectionClass[]
     */
    private $reflectionClassCache;

    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function generate(RouteCollection $routeCollection)
    {
        $openapiPaths = new OpenApi\Paths([]);
        $openapi = new OpenApi\OpenApi([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Open API Specification',
                'version' => '1.0.0',
            ],
            'paths' => $openapiPaths,
        ]);

        foreach ($routeCollection as $route) {
            [$controllerClass, $actionName] = explode('::', $route->getDefault('_controller'));

            $controllerReflectionClass = $this->getReflectionByClass($controllerClass);
            $actionReflectionMethod = $controllerReflectionClass->getMethod($actionName);

            $annotation = $this->annotationReader->getMethodAnnotation($actionReflectionMethod, RestApiBundle\Annotation\Docs\Endpoint::class);
            if (!$annotation instanceof RestApiBundle\Annotation\Docs\Endpoint) {
                continue;
            }

            $docBlock = $this->docBlockFactory->create($actionReflectionMethod->getDocComment());

            if ($docBlock->getTagsByName('return')) {
                if (count($docBlock->getTagsByName('return')) > 1) {
                    throw new RestApiBundle\Exception\Docs\InvalidEndpointException('DocBlock contains more then one @return tag.', $controllerClass, $actionName);
                }

                $docBlockReturnType = $docBlock->getTagsByName('return')[0];
                if (!$docBlockReturnType instanceof Return_) {
                    throw new \InvalidArgumentException();
                }

                $responseClass = $this->getResponseClassByReturnDocBlock($docBlockReturnType);
            } elseif ($actionReflectionMethod->getReturnType()) {
                if ($actionReflectionMethod->getReturnType()->allowsNull()) {
                    throw new \InvalidArgumentException('Not implemented.');
                }

                $responseClass = (string) $actionReflectionMethod->getReturnType();
            } else {
                throw new RestApiBundle\Exception\Docs\InvalidEndpointException('Return type not specified.', $controllerClass, $actionName);
            }

            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($responseClass)) {
                throw new \InvalidArgumentException('Not implemented');
            }

            $openapiOperation = new OpenApi\Operation([
                'summary' => $annotation->title,
                'responses' => [
                    200 => $this->getOpenApiResponseByResponseModelClass($responseClass),
                ]
            ]);

            if ($annotation->tags) {
                $openapiOperation->tags = $annotation->tags;
            }

            if ($annotation->description) {
                $openapiOperation->description = $annotation->description;
            }

            $openapiPathItem = new OpenApi\PathItem([]);
            foreach ($route->getMethods() as $method) {
                $method = strtolower($method);
                $openapiPathItem->{$method} = $openapiOperation;
            }

            $openapiPaths->addPath($route->getPath(), $openapiPathItem);
            //var_dump($annotation);
//            var_dump(, $actionReflectionMethod->getParameters());
            //var_dump($route->getMethods(), $route->getPath(), $route->getDefault('_controller'), $controllerClass, $actionName);
        }

        var_dump(\cebe\openapi\Writer::writeToYaml($openapi));
    }

    private function getOpenApiResponseByResponseModelClass(string $class): OpenApi\Response
    {
        $responseModelReflection = $this->getReflectionByClass($class);
        $methods = $responseModelReflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $properties = [];

        foreach ($methods as $method) {
            if (strpos($method->getName(), 'get') !== 0) {
                continue;
            }

            $propertyName = lcfirst(substr($method->getName(), 3));

            $returnType = (string) $responseModelReflection->getMethod($method->getName())->getReturnType();

            switch ($returnType) {
                case 'int':
                    $properties[$propertyName] = [
                        'type' => 'number',
                    ];

                    break;

                case 'string':
                    $properties[$propertyName] = [
                        'type' => 'string',
                    ];

                    break;

                default:
                    throw new \InvalidArgumentException('Not implemented.');
            }
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = [
            'type' => 'string',
        ];

        $response = new OpenApi\Response([
            'description' => 'Success',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => $properties,
                    ]
                ]
            ]
        ]);

        return $response;
    }

    private function getResponseClassByReturnDocBlock(Return_ $returnDocBlock): string
    {
        if ($returnDocBlock->getType() instanceof Object_) {
            $class = (string) $returnDocBlock->getType();
        } else {
            throw new \InvalidArgumentException('Not implemented.');
        }

        return $class;
    }

    private function getReflectionByClass(string $class): \ReflectionClass
    {
        $class = rtrim($class, '\\');

        if (!isset($this->reflectionClassCache[$class])) {
            $this->reflectionClassCache[$class] = new \ReflectionClass($class);
        }

        return $this->reflectionClassCache[$class];
    }

    private function isGetMethod(\ReflectionMethod $method): bool
    {
        $methodLength = strlen($method->name);
        $getOrIs = ((strpos($method->name, 'get') === 0 && $methodLength > 3) || (strpos($method->name, 'is') === 0 && $methodLength > 2));

        return !$method->isStatic() && ($getOrIs && $method->getNumberOfRequiredParameters() === 0);
    }
}
