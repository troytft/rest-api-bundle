<?php

namespace RestApiBundle\CacheWarmer\Mapper;

use RestApiBundle;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use function sprintf;

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
        try {
            return $this->cacheSchemaResolver->warmUpCache($this->settingsProvider->getSourceCodeDirectory());
        } catch (RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface $exception) {
            throw new \RuntimeException(sprintf('An error occurred: %s – %s', $exception->getContext(), $exception->getMessage()));
        }
    }

    public function isOptional()
    {
        return true;
    }
}
