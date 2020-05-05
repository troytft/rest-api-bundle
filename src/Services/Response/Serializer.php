<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;
use function var_dump;

class Serializer
{
    /**
     * @var int
     */
    private $jsonEncodeOptions = 0;

    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;

    public function __construct(RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
        foreach ($settingsProvider->getResponseJsonEncodeOptions() as $jsonEncodeOption) {
            $this->jsonEncodeOptions |= $jsonEncodeOption;
        }

        $normalizers = [
            new RestApiBundle\Services\Response\GetSetMethodNormalizer(),
            new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer(\DATE_ATOM, new \DateTimeZone('UTC')),
        ];
        $encoders = [
            new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
        ];

        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    }

    public function toJson(RestApiBundle\ResponseModelInterface $responseModel): string
    {
        return $this->serializer->serialize($responseModel, 'json', [
            'json_encode_options' => $this->jsonEncodeOptions,
        ]);
    }
}
