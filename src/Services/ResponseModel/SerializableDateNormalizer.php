<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SerializableDateNormalizer implements NormalizerInterface
{
    public const FORMAT_KEY = 'date_format';

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\DateInterface;
    }

    /**
     * @param RestApiBundle\Mapping\ResponseModel\DateInterface $object
     * @param array<string, mixed> $context
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): string
    {
        return $object->getValue()
            ->format($context[static::FORMAT_KEY]);
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            RestApiBundle\Mapping\ResponseModel\DateInterface::class => true,
        ];
    }
}
