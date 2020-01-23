<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;
use function get_class;
use function strpos;

class GetSetMethodNormalizer extends \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer
{
    public const ATTRIBUTE_TYPENAME = '__typename';

    public function supportsNormalization($data, $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof RestApiBundle\ResponseModelInterface && $this->supports(get_class($data));
    }

    private function supports($class)
    {
        $class = RestApiBundle\Services\ReflectionClassStore::get($class);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (strpos($method->getName(), 'get') === 0) {
                return true;
            }
        }

        return false;
    }

    public function extractAttributes($object, $format = null, array $context = [])
    {
        if (!$object instanceof RestApiBundle\ResponseModelInterface) {
            throw new \InvalidArgumentException();
        }

        $attributes = parent::extractAttributes($object, $format, $context);
        $attributes[] = static::ATTRIBUTE_TYPENAME;

        return $attributes;
    }

    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        if (!$object instanceof RestApiBundle\ResponseModelInterface) {
            throw new \InvalidArgumentException();
        }

        if ($attribute === static::ATTRIBUTE_TYPENAME) {
            $typenameResolver = new ResponseModelTypenameResolver();
            $typename = $typenameResolver->resolve(get_class($object));

            return $typename;
        }

        return parent::getAttributeValue($object, $attribute, $format, $context);
    }
}
