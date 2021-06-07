<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\Cache;

use Symfony\Component\Finder\Finder;
use function count;
use function get_declared_classes;
use function ltrim;
use function var_dump;

class CacheSchemaResolver implements RestApiBundle\Services\Mapper\SchemaResolverInterface
{
    private const CACHE_FILENAME = 'mapper_schema.php.cache';

    private RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver;
    private Cache\Adapter\PhpArrayAdapter $cacheAdapter;
    private string $projectDir;

    public function __construct(
        RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver,
        string $cacheDir,
        string $projectDir
    ) {
        $this->schemaResolver = $schemaResolver;
        $this->projectDir = $projectDir;
        $this->cacheAdapter = new Cache\Adapter\PhpArrayAdapter(
            $cacheDir . \DIRECTORY_SEPARATOR . static::CACHE_FILENAME,
            new Cache\Adapter\NullAdapter()
        );
    }

    public function resolve(string $class, bool $isNullable = false): RestApiBundle\Model\Mapper\Schema
    {
        $cacheItem = $this->cacheAdapter->getItem($this->resolveCacheKey($class, $isNullable));

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->schemaResolver->resolve($class, $isNullable));
        }

        return $cacheItem->get();
    }

    /**
     * @return string[]
     */
    public function warmUp(): array
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($this->projectDir)
            ->name('*.php');

        $values = [];

        foreach ($finder as $fileInfo) {
            $class = RestApiBundle\Helper\PhpFileParserHelper::getClassByFileInfo($fileInfo);
            if (!$class || !RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($class)) {
                continue;
            }

//            $values[$this->resolveCacheKey($class, false)] = $this->resolve($class);
        }

        var_dump($values);
        die();

        return [];
    }

    private function resolveCacheKey(string $class, bool $isNullable): string
    {
        return strtr(ltrim('\\', $class), '\\', '.') . $isNullable;
    }
}
