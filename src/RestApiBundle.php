<?php

namespace RestApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RestApiBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DependencyInjection\RestApiBundleExtension();
    }
}
