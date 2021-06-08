<?php

namespace RestApiBundle\CacheWarmer\Mapper;

use RestApiBundle;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use function var_dump;

class SchemaCacheWarmer implements CacheWarmerInterface
{
    private RestApiBundle\Services\Mapper\CacheSchemaResolver $cacheSchemaResolver;

    public function __construct(RestApiBundle\Services\Mapper\CacheSchemaResolver $cacheSchemaResolver)
    {
        $this->cacheSchemaResolver = $cacheSchemaResolver;
    }

    public function warmUp($cacheDir)
    {
        var_dump($this->cacheSchemaResolver->warmUpCache());die();
    }

    public function isOptional()
    {
        return false;
    }
}
