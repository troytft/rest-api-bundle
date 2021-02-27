<?php

namespace RestApiBundle\CacheWarmer\ResponseModel;

use RestApiBundle;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use function var_dump;


class NormalizerCacheWarmer implements CacheWarmerInterface
{
    private const CACHE_FILENAME = 'rest-api-bundle/normalizer-cache.php';

    /**
     * @var RestApiBundle\SettingsProvider\KernelSettingsProvider
     */
    private $kernelSettingsProvider;

    /**
     * @var RestApiBundle\Services\Response\ResponseModelNormalizer
     */
    private $responseModelNormalizer;

    public function __construct(
        RestApiBundle\SettingsProvider\KernelSettingsProvider $kernelSettingsProvider,
        RestApiBundle\Services\Response\ResponseModelNormalizer $responseModelNormalizer
    ) {
        $this->kernelSettingsProvider = $kernelSettingsProvider;
        $this->responseModelNormalizer = $responseModelNormalizer;
    }

    public function warmUp(string $cacheDir)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($this->findResponseModelDirectories())
            ->name('*.php');

        foreach ($finder as $fileInfo) {
            $class = RestApiBundle\Helper\ClassHelper::extractClassByFileInfo($fileInfo);
            if (!RestApiBundle\Helper\ClassInterfaceChecker::isResponseModel($class)) {
                continue;
            }

            $reflectionClass = new \ReflectionClass($class);
            $instance = $reflectionClass->newInstance();
            $result = $this->responseModelNormalizer->extractAttributes($instance);
            var_dump($result);
        }

        $cacheAdapter = new PhpArrayAdapter(static::CACHE_FILENAME, new NullAdapter());
//        $cacheAdapter->w

        var_dump($cacheDir);die();
    }

    private function findResponseModelDirectories(): array
    {
        $finder = new Finder();
        $finder
            ->directories()
            ->in($this->kernelSettingsProvider->getProjectDir())
            ->name(RestApiBundle\Services\Response\TypenameResolver::NAMESPACE_NAME);

        $result = [];
        foreach ($finder as $fileInfo) {
            $result[] = $fileInfo->getPathname();
        }

        return $result;
    }

    public function isOptional()
    {
        return false;
    }
}
