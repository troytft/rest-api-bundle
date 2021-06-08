<?php

namespace RestApiBundle\CacheWarmer\Mapper;

use RestApiBundle;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class SchemaCacheWarmer implements CacheWarmerInterface
{
    private RestApiBundle\Services\Mapper\CacheSchemaResolver $cacheSchemaResolver;

    public function __construct(RestApiBundle\Services\Mapper\CacheSchemaResolver $cacheSchemaResolver)
    {
        $this->cacheSchemaResolver = $cacheSchemaResolver;
    }

    public function warmUp($cacheDir)
    {
        return $this->cacheSchemaResolver->warmUpCache();
    }

    public function isOptional()
    {
        return false;
    }
}
