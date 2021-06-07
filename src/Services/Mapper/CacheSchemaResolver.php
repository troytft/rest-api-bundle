<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use Symfony\Component\Cache;

use function count;
use function get_declared_classes;
use function ltrim;
use function var_dump;

class CacheSchemaResolver implements RestApiBundle\Services\Mapper\SchemaResolverInterface
{
    private const CACHE_FILENAME = 'mapper_schema.php.cache';

    private RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver;
    private Cache\Adapter\PhpArrayAdapter $cacheAdapter;

    public function __construct(RestApiBundle\Services\Mapper\SchemaResolver $schemaResolver, string $cacheDir)
    {
        $this->schemaResolver = $schemaResolver;
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
        foreach (get_declared_classes() as $class) {
            var_dump($class);
            if (!RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($class)) {
                continue;
            }

            print $class;

            $this->resolve($class);
        }

        var_dump('warm');die();

        return [];
    }

    private function resolveCacheKey(string $class, bool $isNullable): string
    {
        return strtr(ltrim('\\', $class), '\\', '.') . $isNullable;
    }
}
