<?php

namespace RestApiBundle\CacheWarmer\ResponseModel;

use RestApiBundle;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use function class_implements;
use function get_declared_classes;
use function is_dir;
use function is_writable;
use function mkdir;
use function scandir;
use function sprintf;
use function str_contains;
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
        var_dump($this->kernelSettingsProvider->getProjectDir());die();
        $namespacePart = sprintf('\%s\\', RestApiBundle\Services\Response\TypenameResolver::NAMESPACE_NAME);
        foreach (get_declared_classes() as $class) {
//            if (!str_contains($class, $namespacePart) || !RestApiBundle\Helper\ClassInterfaceChecker::isResponseModel($class)) {
//                continue;
//            }

            var_dump($class);
        }

        var_dump($cacheDir);die();
    }

    public function isOptional()
    {
        return false;
    }
}
