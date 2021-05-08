<?php

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;

use function get_class;

class ResponseModelNormalizer extends \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer
{
    public const ATTRIBUTE_TYPENAME = '__typename';

    /**
     * @var RestApiBundle\Services\ResponseModel\TypenameResolver
     */
    private $typenameResolver;

    public function __construct(RestApiBundle\Services\ResponseModel\TypenameResolver $typenameResolver)
    {
        parent::__construct();

        $this->typenameResolver = $typenameResolver;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface;
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
            $result = $this->typenameResolver->resolve(get_class($object));
        } else {
            $result = parent::getAttributeValue($object, $attribute, $format, $context);
        }

        return $result;
    }
}
