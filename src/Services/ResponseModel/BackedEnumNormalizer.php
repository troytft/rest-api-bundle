<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BackedEnumNormalizer implements NormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof \BackedEnum;
    }

    /**
     * @param \BackedEnum $object
     * @param array<string, mixed> $context
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): string|int
    {
        return $object->value;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            \BackedEnum::class => true,
        ];
    }
}
