<?php

namespace RestApiBundle\Services\ResponseModel;

use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class BackedEnumNormalizer implements ContextAwareNormalizerInterface
{
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof \BackedEnum;
    }

    /**
     * @param \BackedEnum $object
     */
    public function normalize($object, $format = null, array $context = []): float|array|\ArrayObject|bool|int|string|null
    {
        return $object->value;
    }
}
