<?php

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class Serializer
{
    /**
     * @var RestApiBundle\Services\SettingsProvider
     */
    private $settingsProvider;

    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;

    public function __construct(
        RestApiBundle\Services\SettingsProvider $settingsProvider,
        RestApiBundle\Services\ResponseModel\ResponseModelNormalizer $responseModelNormalizer
    ) {
        $this->settingsProvider = $settingsProvider;

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
