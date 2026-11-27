<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SerializableEnumNormalizer implements NormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\EnumInterface;
    }

    /**
     * @param RestApiBundle\Mapping\ResponseModel\EnumInterface $object
     * @param array<string, mixed> $context
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): string|int
    {
        return $object->getValue();
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            RestApiBundle\Mapping\ResponseModel\EnumInterface::class => true,
        ];
    }
}
