<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class SerializableEnumNormalizer implements ContextAwareNormalizerInterface
{
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\SerializableEnumInterface;
    }

    /**
     * @param RestApiBundle\Mapping\ResponseModel\SerializableEnumInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->getValue();
    }
}
