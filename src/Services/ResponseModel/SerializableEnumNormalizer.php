<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class SerializableEnumNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\EnumInterface;
    }

    /**
     * @param RestApiBundle\Mapping\ResponseModel\EnumInterface $object
     * @param array<string, mixed> $context
     */
    public function normalize($object, $format = null, array $context = []): string|int
    {
        return $object->getValue();
    }
}
