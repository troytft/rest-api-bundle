<?php declare(strict_types=1);

namespace RestApiBundle\Helper\OpenApi;

class FileTypeResolver
{
    public const YAML_TYPE = 'yaml';
    public const JSON_TYPE = 'json';

    public static function resolveByFilename(string $filename): string
    {
        return match (pathinfo($filename, \PATHINFO_EXTENSION)) {
            'yml', 'yaml' => static::YAML_TYPE,
            'json' => static::JSON_TYPE,
            default => throw new \InvalidArgumentException('Invalid file extension'),
        };
    }
}
