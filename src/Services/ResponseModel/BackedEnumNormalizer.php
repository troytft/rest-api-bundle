<?php declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class BackedEnumNormalizer implements ContextAwareNormalizerInterface
{
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof \BackedEnum;
    }

    /**
     * @param \BackedEnum $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->value;
    }
}
