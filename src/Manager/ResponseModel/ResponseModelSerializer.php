<?php

namespace RestApiBundle\Manager\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer;

class ResponseModelSerializer
{
    /**
     * @var Serializer\Serializer
     */
    private $serializer;

    public function __construct()
    {
        $dateTimeNormalizer = new Serializer\Normalizer\DateTimeNormalizer([
            Serializer\Normalizer\DateTimeNormalizer::FORMAT_KEY => \DATE_ATOM,
            Serializer\Normalizer\DateTimeNormalizer::TIMEZONE_KEY => new \DateTimeZone('UTC'),
        ]);

        $normalizers = [
            new RestApiBundle\Manager\ResponseModel\GetSetMethodNormalizer(),
            $dateTimeNormalizer
        ];
        $encoders = [
            new Serializer\Encoder\JsonEncoder()
        ];

        $this->serializer = new Serializer\Serializer($normalizers, $encoders);
    }

    public function toJson(RestApiBundle\ResponseModelInterface $responseModel): string
    {
        return $this->serializer->serialize($responseModel, 'json');
    }
}
