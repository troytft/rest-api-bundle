<?php

namespace RestApiBundle\Services\OpenApi;

use Symfony\Component\Filesystem;
use Symfony\Component\Yaml;
use cebe\openapi\spec as OpenApi;

class FileAdapter
{
    private const YAML_TYPE = 'yaml';
    private const JSON_TYPE = 'json';

    public function __construct(private Filesystem\Filesystem $filesystem)
    {
    }

    public function write(OpenApi\OpenApi $specification, string $filename): void
    {
        $content = match ($this->resolveTypeByFileExtension($filename)) {
            static::YAML_TYPE => $this->toYaml($specification),
            static::JSON_TYPE => $this->toJson($specification),
        };

        $this->filesystem->dumpFile($filename, $content);
    }

    public function read(string $filename): OpenApi\OpenApi
    {
        $content = file_get_contents($filename);

        return match ($this->resolveTypeByFileExtension($filename)) {
            static::YAML_TYPE => $this->fromYaml($content),
            static::JSON_TYPE => $this->fromJson($content),
        };
    }

    private function toYaml(OpenApi\OpenApi $specification): string
    {
        return Yaml\Yaml::dump($specification->getSerializableData(), 256, 4, Yaml\Yaml::DUMP_OBJECT_AS_MAP);
    }

    private function toJson(OpenApi\OpenApi $specification): string
    {
        $result = json_encode($specification->getSerializableData(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return $result;
    }

    private function fromYaml(string $content): OpenApi\OpenApi
    {
        return $this->fromArray(Yaml\Yaml::parse($content));
    }

    private function fromJson(string $content): OpenApi\OpenApi
    {
        return $this->fromArray(json_decode($content, true));
    }

    private function fromArray(array $data): OpenApi\OpenApi
    {
        return new OpenApi\OpenApi(array_merge([
            'paths' => [],
            'tags' => [],
            'components' => [],
        ], $data));
    }

    private function resolveTypeByFileExtension(string $filename): string
    {
        return match (pathinfo($filename, \PATHINFO_EXTENSION)) {
            'yml', 'yaml' => static::YAML_TYPE,
            'json' => static::JSON_TYPE,
            default => throw new \InvalidArgumentException('Invalid file extension')
        };
    }
}
