<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseModelNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    public const ATTRIBUTE_TYPENAME = '__typename';

    private GetSetMethodNormalizer $normalizer;

    public function __construct()
    {
        $this->normalizer = new GetSetMethodNormalizer();
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->normalizer->setSerializer($serializer);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $result = $this->normalizer->normalize($object, $format, $context);

        if (\is_array($result)) {
            $result[static::ATTRIBUTE_TYPENAME] = RestApiBundle\Helper\ResponseModel\TypenameResolver::resolve($object::class);
        }

        return $result;
    }
}
