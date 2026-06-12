<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class BackedEnumNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof \BackedEnum;
    }

    /**
     * @param \BackedEnum $object
     * @param array<string, mixed> $context
     */
    public function normalize($object, $format = null, array $context = []): string|int
    {
        return $object->value;
    }
}
