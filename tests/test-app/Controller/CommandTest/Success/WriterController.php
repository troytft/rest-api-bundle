<?php

namespace TestApp\Controller\CommandTest\Success;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

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
    public function createAction(TestApp\RequestModel\WriterData $requestModel): TestApp\ResponseModel\Writer
    {
        return new TestApp\ResponseModel\Writer(new TestApp\Entity\Writer());
    }

    /**
     * @Docs\Endpoint(title="Writers list with filters", tags={"writers"})
     *
     * @Route(methods="GET")
     *
     * @return TestApp\ResponseModel\Writer[]
     */
    public function listAction(TestApp\RequestModel\WriterList $requestModel)
    {
        return [];
    }

    /**
     * @Docs\Endpoint(title="Remove writer", tags={"writers"})
     *
     * @Route("/{id}", methods="DELETE", requirements={"id": "\d"})
     */
    public function removeAction(TestApp\Entity\Writer $writer): void
    {
    }
}
