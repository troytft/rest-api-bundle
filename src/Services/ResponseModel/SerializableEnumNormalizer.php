<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class SerializableEnumNormalizer implements ContextAwareNormalizerInterface
{
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\EnumInterface;
    }

    /**
     * @param RestApiBundle\Mapping\ResponseModel\EnumInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->getValue();
    }
}
