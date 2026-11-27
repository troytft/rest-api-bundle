<?php

declare(strict_types=1);

namespace RestApiBundle\CacheWarmer\Mapper;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class SchemaCacheWarmer implements CacheWarmerInterface
{
    //    private RestApiBundle\Services\Mapper\CacheSchemaResolver $cacheSchemaResolver;
    //    private RestApiBundle\Services\SettingsProvider $settingsProvider;
    //
    //    public function __construct(
    //        RestApiBundle\Services\Mapper\CacheSchemaResolver $cacheSchemaResolver,
    //        RestApiBundle\Services\SettingsProvider $settingsProvider
    //    ) {
    //        $this->cacheSchemaResolver = $cacheSchemaResolver;
    //        $this->settingsProvider = $settingsProvider;
    //    }

    /**
     * @return string[]
     */
    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        return []; // return $this->cacheSchemaResolver->warmUpCache($this->settingsProvider->getSourceCodeDirectory());
    }

    public function isOptional(): bool
    {
        return true;
    }
}
