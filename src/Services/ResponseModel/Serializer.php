<?php

declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class Serializer
{
    private \Symfony\Component\Serializer\Serializer $serializer;

    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        ResponseModelNormalizer $responseModelNormalizer,
    ) {
        $normalizers = [
            $responseModelNormalizer,
            new SerializableDateNormalizer(),
            new SerializableEnumNormalizer(),
            new BackedEnumNormalizer(),
            new DateTimeNormalizer(),
        ];
        $encoders = [
            new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
        ];

        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    }

    public function serialize($value): ?string
    {
        if ($value === null) {
            $result = null;
        } elseif ($value instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface) {
            $result = $this->responseModelToJson($value);
        } elseif (is_array($value)) {
            if (!array_is_list($value)) {
                throw new \InvalidArgumentException('Associative arrays are not allowed');
            }

            $chunks = [];

            foreach ($value as $item) {
                if (!$item instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface) {
                    throw new \InvalidArgumentException('The collection should consist of response models');
                }

                $chunks[] = $this->responseModelToJson($item);
            }

            $result = '['.join(',', $chunks).']';
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function responseModelToJson(RestApiBundle\Mapping\ResponseModel\ResponseModelInterface $responseModel): string
    {
        return $this->serializer->serialize($responseModel, 'json', [
            SerializableDateNormalizer::FORMAT_KEY => $this->settingsProvider->getResponseModelDateFormat(),
            JsonEncode::OPTIONS => $this->settingsProvider->getResponseJsonEncodeOptions(),
            DateTimeNormalizer::FORMAT_KEY => $this->settingsProvider->getResponseModelDateTimeFormat(),
            DateTimeNormalizer::TIMEZONE_KEY => new \DateTimeZone('UTC'),
        ]);
    }
}
