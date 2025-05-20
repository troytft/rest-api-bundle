<?php

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class SerializableDateNormalizer implements ContextAwareNormalizerInterface
{
    public const FORMAT_KEY = 'date_format';

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\DateInterface;
    }

    /**
     * @param RestApiBundle\Mapping\ResponseModel\DateInterface $object
     */
    public function normalize($object, $format = null, array $context = []): float|array|\ArrayObject|bool|int|string|null
    {
        return $object->getValue()->format($context[static::FORMAT_KEY]);
    }
}
