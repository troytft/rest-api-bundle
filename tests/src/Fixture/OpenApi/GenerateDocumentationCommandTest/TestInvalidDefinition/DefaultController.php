<?php

declare(strict_types=1);

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestInvalidDefinition;

use RestApiBundle\Mapping\OpenApi as Docs;
use Symfony\Component\Routing\Annotation\Route;

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
