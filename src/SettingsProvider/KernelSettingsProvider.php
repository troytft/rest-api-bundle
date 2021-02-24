<?php

namespace RestApiBundle\SettingsProvider;

use RestApiBundle;

class KernelSettingsProvider extends RestApiBundle\SettingsProvider\BaseSettingsProvider
{
    public function getProjectDir(): string
    {
        return $this->parameterBag->get('kernel.project_dir');
    }
}
