<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class SerializableDateNormalizer implements ContextAwareNormalizerInterface
{
    public const FORMAT_KEY = 'date_format';

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\SerializableDateInterface;
    }

    /**
     * @param RestApiBundle\Mapping\ResponseModel\SerializableDateInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->getValue()->format($context[static::FORMAT_KEY]);
    }
}
