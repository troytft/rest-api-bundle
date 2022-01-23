<?php

namespace Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success;

use Tests;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi as Docs;

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
    public function createAction(Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success\RequestModel\WriterData $requestModel): Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success\ResponseModel\Author
    {
        return new Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success\ResponseModel\Author();
    }

    /**
     * @Docs\Endpoint(title="Writers list with filters", tags={"writers"})
     *
     * @Route(methods="GET")
     *
     * @return Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success\ResponseModel\Author[]
     */
    public function listAction(Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success\RequestModel\WriterList $requestModel)
    {
        return [];
    }

    /**
     * @Docs\Endpoint(title="Remove writer", tags={"writers"})
     *
     * @Route("/{id}", methods="DELETE", requirements={"id": "\d"})
     */
    public function removeAction(Tests\Fixture\TestApp\Entity\Author $writer): void
    {
    }
}
