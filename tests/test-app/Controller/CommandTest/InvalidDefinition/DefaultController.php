<?php

namespace TestApp\Controller\CommandTest\InvalidDefinition;

use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class DefaultController
{
    /**
     * @Docs\Endpoint(title="Title", tags={"tag"})
     *
     * @Route("/{unknown_parameter}", methods="GET")
     *
     * @return null
     */
    public function testAction()
    {
        return null;
    }
}