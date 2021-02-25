<?php

namespace RestApiBundle\CacheWarmer\ResponseModel;

use RestApiBundle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use function var_dump;


class NormalizerCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var RestApiBundle\SettingsProvider\KernelSettingsProvider
     */
    private $kernelSettingsProvider;

    public function __construct(RestApiBundle\SettingsProvider\KernelSettingsProvider $kernelSettingsProvider)
    {
        $this->kernelSettingsProvider = $kernelSettingsProvider;
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

            var_dump($class);
        }


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
