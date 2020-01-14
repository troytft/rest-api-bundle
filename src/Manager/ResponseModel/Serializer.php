<?php

namespace RestApiBundle\Manager\ResponseModel;

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
            new RestApiBundle\Manager\ResponseModel\GetSetMethodNormalizer(),
            $dateTimeNormalizer
        ];
        $encoders = [
            new \Symfony\Component\Serializer\Encoder\JsonEncoder()
        ];

        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    }

    public function toJson(RestApiBundle\ResponseModelInterface $responseModel): string
    {
        return $this->serializer->serialize($responseModel, 'json');
    }
}
