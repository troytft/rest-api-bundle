<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

use function get_class;

class ResponseModelNormalizer extends \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer
{
    /**
     * @var array<string, string[]>
     */
    private $attributesCache = [];

    public const ATTRIBUTE_TYPENAME = '__typename';

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RestApiBundle\ResponseModelInterface;
    }

    public function extractAttributes($object, $format = null, array $context = [])
    {
        if (!$object instanceof RestApiBundle\ResponseModelInterface) {
            throw new \InvalidArgumentException();
        }

        $key = get_class($object);
        if (!isset($this->attributesCache[$key])) {
            $this->attributesCache[$key] = parent::extractAttributes($object, $format, $context);
            $this->attributesCache[$key][] = static::ATTRIBUTE_TYPENAME;
        }

        return $this->attributesCache[$key];
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
