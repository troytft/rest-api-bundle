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

    /**
     * @param RestApiBundle\ResponseModelInterface $object
     */
    public function extractAttributes($object, $format = null, array $context = [])
    {
        $result = parent::extractAttributes($object, $format, $context);
        $result[] = static::ATTRIBUTE_TYPENAME;

        return $result;
    }

    /**
     * @param RestApiBundle\ResponseModelInterface $object
     */
    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        if ($attribute === static::ATTRIBUTE_TYPENAME) {
            $result = $this->resolveTypename($object);
        } else {
            $result = parent::getAttributeValue($object, $attribute, $format, $context);
        }

        return $result;
    }

    private function resolveTypename(RestApiBundle\ResponseModelInterface $responseModel): string
    {
        $key = get_class($responseModel);
        if (!isset($this->typenameCache[$key])) {
            $this->typenameCache[$key] = $this->typenameResolver->resolve($key);
        }

        return $this->typenameCache[$key];
    }
}
