<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

use function lcfirst;
use function strlen;
use function strpos;
use function substr;

class AttributesExtractor implements AttributesExtractorInterface
{
    /**
     * {@inheritDoc}
     */
    public function extractByClass(string $class): array
    {
        return $this->extractByReflectionClass(new \ReflectionClass($class));
    }

    /**
     * {@inheritDoc}
     */
    public function extractByInstance(RestApiBundle\ResponseModelInterface $instance): array
    {
        return $this->extractByReflectionClass(new \ReflectionObject($instance));
    }

    /**
     * @return string[]
     */
    private function extractByReflectionClass(\ReflectionClass $reflectionClass): array
    {
        $result = [];
        $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectionMethods as $reflectionMethod) {
            if (!$this->isGetMethod($reflectionMethod)) {
                continue;
            }

            $result[] = lcfirst(substr($reflectionMethod->name, strpos($reflectionMethod->name, 'is') === 0 ? 2 : 3));
        }

        $result[] = RestApiBundle\Services\Response\TypenameResolver::ATTRIBUTE_NAME;

        return $result;
    }

    private function isGetMethod(\ReflectionMethod $method): bool
    {
        if ($method->isStatic() || $method->getNumberOfRequiredParameters() > 0) {
            return false;
        }

        $methodLength = strlen($method->name);

        return (strpos($method->name, 'get') === 0 && $methodLength > 3)
            || (strpos($method->name, 'is') === 0 && $methodLength > 2)
            || (strpos($method->name, 'has') === 0 && $methodLength > 3);
    }
}
