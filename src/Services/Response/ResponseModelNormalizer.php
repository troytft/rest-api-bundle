<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

use function get_class;

class ResponseModelNormalizer extends \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer
{
    /**
     * @var RestApiBundle\Services\Response\TypenameResolver
     */
    private $typenameResolver;

    /**
     * @var RestApiBundle\Services\Response\AttributesExtractorInterface
     */
    private $attributesExtractor;

    public function __construct(
        RestApiBundle\Services\Response\TypenameResolver $typenameResolver,
        RestApiBundle\Services\Response\AttributesExtractorInterface $attributesExtractor
    ) {
        parent::__construct();

        $this->typenameResolver = $typenameResolver;
        $this->attributesExtractor = $attributesExtractor;
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
        return $this->attributesExtractor->extractByInstance($object);
    }

    /**
     * @param RestApiBundle\ResponseModelInterface $object
     */
    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        if ($attribute === RestApiBundle\Services\Response\TypenameResolver::ATTRIBUTE_NAME) {
            $result = $this->typenameResolver->resolve(get_class($object));
        } else {
            $result = parent::getAttributeValue($object, $attribute, $format, $context);
        }

        return $result;
    }
}
