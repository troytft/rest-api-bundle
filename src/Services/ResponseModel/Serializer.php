<?php

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class Serializer
{
    private \Symfony\Component\Serializer\Serializer $serializer;

    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        RestApiBundle\Services\ResponseModel\ResponseModelNormalizer $responseModelNormalizer
    ) {
        $normalizers = [
            $responseModelNormalizer,
            new RestApiBundle\Services\ResponseModel\SerializableDateNormalizer(),
            new RestApiBundle\Services\ResponseModel\SerializableEnumNormalizer(),
            new DateTimeNormalizer(),
        ];
        $encoders = [
            new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
        ];

        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    }

    public function toJson(RestApiBundle\Mapping\ResponseModel\ResponseModelInterface $responseModel): string
    {
        return $this->serializer->serialize($responseModel, 'json', [
            RestApiBundle\Services\ResponseModel\SerializableDateNormalizer::FORMAT_KEY => $this->settingsProvider->getResponseModelDateFormat(),
            JsonEncode::OPTIONS => $this->settingsProvider->getResponseJsonEncodeOptions(),
            DateTimeNormalizer::FORMAT_KEY => $this->settingsProvider->getResponseModelDateTimeFormat(),
            DateTimeNormalizer::TIMEZONE_KEY => new \DateTimeZone('UTC'),
        ]);
    }
}
