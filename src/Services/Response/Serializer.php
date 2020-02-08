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
        $encoder = new \Symfony\Component\Serializer\Encoder\JsonEncoder();

        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, [$encoder]);
    }

    public function toJson(RestApiBundle\ResponseModelInterface $responseModel): string
    {
        return $this->serializer->serialize($responseModel, 'json', [
            'json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ]);
    }
}
