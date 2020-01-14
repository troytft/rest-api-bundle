<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

class GetSetMethodNormalizer extends \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer
{
    private const ATTRIBUTE_TYPENAME = '__typename';

    /**
     * @var array
     */
    private $typenameCache = [];

    public function supportsNormalization($data, $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof RestApiBundle\ResponseModelInterface && $this->supports(get_class($data));
    }

    private function supports($class)
    {
        $class = new \ReflectionClass($class);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($this->isGetMethod($method)) {
                return true;
            }
        }

        return false;
    }

    private function isGetMethod(\ReflectionMethod $method): bool
    {
        $methodLength = strlen($method->name);
        $getOrIs = ((strpos($method->name, 'get') === 0 && $methodLength > 3) || (strpos($method->name, 'is') === 0 && $methodLength > 2));

        return !$method->isStatic() && ($getOrIs && $method->getNumberOfRequiredParameters() === 0);
    }

    protected function extractAttributes($object, $format = null, array $context = [])
    {
        $attributes = parent::extractAttributes($object, $format, $context);
        if ($object instanceof RestApiBundle\ResponseModelInterface) {
            $attributes[] = static::ATTRIBUTE_TYPENAME;
        }

        return $attributes;
    }

    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        if ($attribute === static::ATTRIBUTE_TYPENAME && $object instanceof RestApiBundle\ResponseModelInterface) {
            return $this->resolveTypename($object);
        }

        return parent::getAttributeValue($object, $attribute, $format, $context);
    }

    private function resolveTypename(RestApiBundle\ResponseModelInterface $responseModel): string
    {
        $className = get_class($responseModel);

        if (!isset($this->typenameCache[$className])) {
            $parts = [];
            $isResponseModel = false;

            foreach (explode('\\', $className) as $part) {
                if ($isResponseModel) {
                    $parts[] = $part;
                } elseif ($part === 'ResponseModel') {
                    $isResponseModel = true;
                }
            }

            if (!$isResponseModel) {
                throw new \RuntimeException(
                    sprintf('Response model "%s" must be in "ResponseModel" namespace', $className)
                );
            }

            $typename = join('_', $parts);
            if (!$typename) {
                throw new \RuntimeException(
                    sprintf('Response model "%s" must have typename', $className)
                );
            }

            $this->typenameCache[$className] = $typename;
        }

        return $this->typenameCache[$className];
    }
}
