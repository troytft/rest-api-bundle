<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

class Serializer
{
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;

    public function __construct()
    {
        $dateTimeNormalizer = new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer([
            \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer::FORMAT_KEY => \DATE_ATOM,
            \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer::TIMEZONE_KEY => new \DateTimeZone('UTC'),
        ]);

        $normalizers = [
            new RestApiBundle\Services\Response\GetSetMethodNormalizer(),
            $dateTimeNormalizer
        ];
        $encoder = new \Symfony\Component\Serializer\Encoder\JsonEncoder(
            new \Symfony\Component\Serializer\Encoder\JsonEncode([JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES])
        );

        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, [$encoder]);
    }

    public function toJson(RestApiBundle\ResponseModelInterface $responseModel): string
    {
        return $this->serializer->serialize($responseModel, 'json');
    }
}
