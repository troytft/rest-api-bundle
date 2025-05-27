<?php

declare(strict_types=1);

namespace RestApiBundle\Services\OpenApi;

use cebe\openapi\spec as OpenApi;
use Symfony\Component\Yaml\Yaml;

final class SchemaSerializer
{
    public function toYaml(OpenApi\OpenApi $specification): string
    {
        return Yaml::dump($specification->getSerializableData(), 256, 4, Yaml::DUMP_OBJECT_AS_MAP | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
    }

    public function toJson(OpenApi\OpenApi $specification): string
    {
        $result = \json_encode($specification->getSerializableData(), \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT);
        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(\json_last_error_msg());
        }

        return $result;
    }

    public function fromYaml(string $content): OpenApi\OpenApi
    {
        return $this->fromArray(Yaml::parse($content));
    }

    public function fromJson(string $content): OpenApi\OpenApi
    {
        return $this->fromArray(\json_decode($content, true));
    }

    private function fromArray(array $data): OpenApi\OpenApi
    {
        return new OpenApi\OpenApi(\array_merge([
            'paths' => [],
            'tags' => [],
            'components' => [],
        ], $data));
    }
}
