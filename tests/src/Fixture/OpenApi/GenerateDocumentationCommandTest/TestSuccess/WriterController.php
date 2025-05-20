<?php

declare(strict_types=1);

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess;

use RestApiBundle\Mapping\OpenApi as Docs;
use Symfony\Component\Routing\Annotation\Route;
use Tests;

/**
 * @Route("/writers")
 */
class WriterController
{
    /**
     * @Docs\Endpoint(title="Create writer", tags={"writers"})
     *
     * @Route(methods="POST")
     */
    public function createAction(RequestModel\WriterData $requestModel): ResponseModel\Author
    {
        return new ResponseModel\Author();
    }

    /**
     * @Docs\Endpoint(title="Writers list with filters", tags={"writers"})
     *
     * @Route(methods="GET")
     *
     * @return ResponseModel\Author[]
     */
    public function listAction(RequestModel\WriterList $requestModel)
    {
        return [];
    }

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
