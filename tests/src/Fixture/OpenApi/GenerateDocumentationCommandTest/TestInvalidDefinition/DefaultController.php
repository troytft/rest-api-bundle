<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestInvalidDefinition;

use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi;

class DefaultController
{
    #[OpenApi\Endpoint(summary: 'Title', tags: ['tag'])]
    #[Route('/{unknown_parameter}', methods: 'GET')]
    public function testAction(): void
    {
    }
}
