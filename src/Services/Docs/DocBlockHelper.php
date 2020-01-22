<?php

namespace RestApiBundle\Services\Docs;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Object_;
use RestApiBundle;

class DocBlockHelper
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function getReturnTypeByReturnTag(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
    {
        $docBlock = $this->docBlockFactory->create($reflectionMethod->getDocComment());

        $count = count($docBlock->getTagsByName('return'));

        if ($count === 0) {
            return null;
        }

        if ($count > 1) {
            throw new \InvalidArgumentException();
            //throw new RestApiBundle\Exception\Docs\InvalidEndpointException('DocBlock contains more then one @return tag.', $controllerClass, $actionName);
        }

        $returnTag = $docBlock->getTagsByName('return')[0];
        if (!$returnTag instanceof Return_) {
            throw new \InvalidArgumentException();
        }

        if ($returnTag->getType() instanceof Object_) {
            $class = (string) $returnTag->getType();

            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($class)) {
                throw new \InvalidArgumentException('Not implemented');
            }
        } else {
            throw new \InvalidArgumentException('Not implemented.');
        }

        return new RestApiBundle\DTO\Docs\ReturnType\ClassType($class, false);
    }
}
