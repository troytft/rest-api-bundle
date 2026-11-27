<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseModelNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    public const ATTRIBUTE_TYPENAME = '__typename';

    private ObjectNormalizer $normalizer;

    public function __construct()
    {
        $this->normalizer = new ObjectNormalizer();
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->normalizer->setSerializer($serializer);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface;
    }

    /**
     * @param RestApiBundle\Mapping\ResponseModel\ResponseModelInterface $object
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        /** @var array<string, mixed> $result */
        $result = $this->normalizer->normalize($object, $format, $context);
        $result[static::ATTRIBUTE_TYPENAME] = RestApiBundle\Helper\TypenameResolver::resolveWithoutSuffix($object::class, 'ResponseModel');

        return $result;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            RestApiBundle\Mapping\ResponseModel\ResponseModelInterface::class => true,
        ];
    }
}
