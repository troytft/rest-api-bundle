<?php

namespace TestApp\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TestApp;

use function range;
use function sprintf;

/**
 * @Route("/demo-responses")
 */
class DemoResponseController extends AbstractController
{
    /**
     * @Route("/null", methods="GET")
     */
    public function nullAction()
    {
        return null;
    }

    /**
     * @Route("/single-response-model", methods="GET")
     */
    public function singeResponseModelAction()
    {
        $entity = new TestApp\Entity\Genre();
        $entity
            ->setId(1)
            ->setSlug('demo-slug');

        return new TestApp\ResponseModel\Genre($entity);
    }

    /**
     * @Route("/collection-of-response-models", methods="GET")
     */
    public function collectionOfResponseModelsAction()
    {
        $result = [];

        foreach (range(1, 3) as $id) {
            $entity = new TestApp\Entity\Genre();
            $entity
                ->setId($id)
                ->setSlug(sprintf('%d-demo-slug', $id));

            $result[] = new TestApp\ResponseModel\Genre($entity);
        }

        return $result;
    }

    /**
     * @Route("/response-class", methods="GET")
     */
    public function responseClassAction()
    {
        return new Response('{"id": 7}', 201);
    }
}
