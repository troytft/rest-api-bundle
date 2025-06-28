<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess;

use Tests;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi;

/**
 * @Route("/writers")
 */
class WriterController
{
    #[OpenApi\Endpoint(summary: 'Create writer')]
    /**
     * @Route(methods="POST")
     */
    public function createAction(Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel\WriterData $requestModel): Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\Author
    {
        return new Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\Author();
    }

    #[OpenApi\Endpoint(summary: 'Writers list with filters', tags: ['writers'])]
    /**
     * @Route(methods="GET")
     *
     * @return Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\Author[]
     */
    public function listAction(Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel\WriterList $requestModel)
    {
        return [];
    }

    #[OpenApi\Endpoint(summary: 'Remove writer', tags: ['writers'])]
    /**
     * @deprecated
     *
     * @Docs\Endpoint(title="Remove writer", tags={"writers"})
     *
     * @Route("/{id}", methods="DELETE", requirements={"id": "\d"})
     */
    public function removeAction(Tests\Fixture\TestApp\Entity\Author $writer): void
    {
    }
}
