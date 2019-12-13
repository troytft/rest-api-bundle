<?php

namespace Common\Helper\Serializer;

use RestApiBundle;

class GetSetMethodNormalizer extends \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer
{
    private const ATTRIBUTE_TYPENAME = '__typename';

    /**
     * @var array
     */
    private $typenameCache = [];

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof Common\ResponseModel\ResponseModelInterface && $this->supports(get_class($data));
    }

    /**
     * Checks if the given class has any get{Property} method.
     *
     * @param string $class
     *
     * @return bool
     */
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

    /**
     * Checks if a method's name is get.* or is.*, and can be called without parameters.
     *
     * @return bool whether the method is a getter or boolean getter
     */
    private function isGetMethod(\ReflectionMethod $method)
    {
        $methodLength = strlen($method->name);
        $getOrIs = ((strpos($method->name, 'get') === 0 && $methodLength > 3) || (strpos($method->name, 'is') === 0 && $methodLength > 2));

        return !$method->isStatic() && ($getOrIs && $method->getNumberOfRequiredParameters() === 0);
    }

    /**
     * @inheritdoc
     */
    protected function extractAttributes($object, $format = null, array $context = [])
    {
        $attributes = parent::extractAttributes($object, $format, $context);
        if ($object instanceof Common\ResponseModel\ResponseModelInterface) {
            $attributes[] = static::ATTRIBUTE_TYPENAME;
        }

        return $attributes;
    }

    /**
     * @inheritdoc
     */
    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        if ($attribute === static::ATTRIBUTE_TYPENAME && $object instanceof Common\ResponseModel\ResponseModelInterface) {
            return $this->resolveTypename($object);
        }

        return parent::getAttributeValue($object, $attribute, $format, $context);
    }

    /**
     * @param Common\ResponseModel\ResponseModelInterface $object
     *
     * @return string
     */
    private function resolveTypename(Common\ResponseModel\ResponseModelInterface $model): string
    {
        $className = get_class($model);

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
