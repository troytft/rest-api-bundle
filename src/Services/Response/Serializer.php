<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

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

    public function __construct(RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;

        $normalizers = [
            new RestApiBundle\Services\Response\GetSetMethodNormalizer(),
            new RestApiBundle\Services\Response\EnumNormalizer(),
            new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer(),
        ];
        $encoders = [
            new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
        ];

        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    }

    public function toJson(RestApiBundle\ResponseModelInterface $responseModel): string
    {
        return $this->serializer->serialize($responseModel, 'json', [
            'json_encode_options' => $this->settingsProvider->getResponseJsonEncodeOptions(),
            'datetime_format' => \DATE_ATOM,
            'datetime_timezone' => new \DateTimeZone('UTC'),
        ]);
    }
}
