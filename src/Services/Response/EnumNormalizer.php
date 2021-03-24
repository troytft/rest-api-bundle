<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class EnumNormalizer implements ContextAwareNormalizerInterface
{
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof RestApiBundle\Enum\Response\SerializableEnumInterface;
    }

    /**
     * @param RestApiBundle\Enum\Response\SerializableEnumInterface $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return $object->getValue();
    }
}
