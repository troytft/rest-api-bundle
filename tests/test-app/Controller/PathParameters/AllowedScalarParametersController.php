<?php

namespace TestApp\Controller\PathParameters;

use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi;

class AllowedScalarParametersController
{
    /**
     * @OpenApi\Endpoint(title="Title", tags={"tag"})
     *
     * @Route("/{int}/{string}", methods="GET", requirements={"int": "\d+", "string": "\w+"})
     *
     * @return null
     */
    public function testAction(int $int, string $string)
    {
        return null;
    }
}
