<?php

namespace TestApp\Controller\CommandTest\Success;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi;

/**
 * @Route("/writers")
 */
class WriterController
{
    /**
     * @OpenApi\Endpoint(title="Create writer", tags={"writers"})
     *
     * @Route(methods="POST")
     */
    public function createAction(TestApp\RequestModel\WriterData $requestModel): TestApp\ResponseModel\Author
    {
        return new TestApp\ResponseModel\Author();
    }

    /**
     * @OpenApi\Endpoint(title="Writers list with filters", tags={"writers"})
     *
     * @Route(methods="GET")
     *
     * @return TestApp\ResponseModel\Author[]
     */
    public function listAction(TestApp\RequestModel\WriterList $requestModel)
    {
        return [];
    }

    /**
     * @OpenApi\Endpoint(title="Remove writer", tags={"writers"})
     *
     * @Route("/{id}", methods="DELETE", requirements={"id": "\d"})
     */
    public function removeAction(TestApp\Entity\Author $writer): void
    {
    }
}
