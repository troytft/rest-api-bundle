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
        $normalizers = [
            new RestApiBundle\Manager\ResponseModel\GetSetMethodNormalizer(),
            new Serializer\Normalizer\DateTimeNormalizer(DATE_ATOM, new \DateTimeZone('UTC'))
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
