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
        $dateTimeNormalizer = new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer(\DATE_ATOM, new \DateTimeZone('UTC'));

        $normalizers = [
            new RestApiBundle\Services\Response\GetSetMethodNormalizer(),
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
