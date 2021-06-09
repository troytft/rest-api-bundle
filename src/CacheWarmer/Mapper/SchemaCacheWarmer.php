<?php

namespace RestApiBundle\CacheWarmer\Mapper;

use RestApiBundle;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class SchemaCacheWarmer implements CacheWarmerInterface
{
    private RestApiBundle\Services\Mapper\CacheSchemaResolver $cacheSchemaResolver;
    private RestApiBundle\Services\SettingsProvider $settingsProvider;

    public function __construct(
        RestApiBundle\Services\Mapper\CacheSchemaResolver $cacheSchemaResolver,
        RestApiBundle\Services\SettingsProvider $settingsProvider
    ) {
        $this->cacheSchemaResolver = $cacheSchemaResolver;
        $this->settingsProvider = $settingsProvider;
    }

    public function warmUp($cacheDir)
    {
        return $this->cacheSchemaResolver->warmUpCache($this->settingsProvider->getSourceCodeDirectory());
    }

    public function isOptional()
    {
        return false;
    }
}
