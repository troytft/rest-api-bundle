<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

use function get_class;

class ResponseModelNormalizer extends \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer
{
    public const ATTRIBUTE_TYPENAME = '__typename';

    /**
     * @var RestApiBundle\Services\Response\TypenameResolver
     */
    private $typenameResolver;

    /**
     * @var array<string, string>
     */
    private $typenameCache = [];

    public function __construct(RestApiBundle\Services\Response\TypenameResolver $typenameResolver)
    {
        parent::__construct();

        $this->typenameResolver = $typenameResolver;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RestApiBundle\ResponseModelInterface;
    }

    public function extractAttributes($object, $format = null, array $context = [])
    {
        $result = parent::extractAttributes($object, $format, $context);
        $result[] = static::ATTRIBUTE_TYPENAME;

        return $result;
    }

    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        if ($attribute === static::ATTRIBUTE_TYPENAME) {
            $key = get_class($object);
            if (!isset($this->typenameCache[$key])) {
                $this->typenameCache[$key] = $this->typenameResolver->resolve($key);
            }
            $result = $this->typenameCache[$key];
        } else {
            $result = parent::getAttributeValue($object, $attribute, $format, $context);
        }

        return $result;
    }
}
