<?php

namespace Tests\TestApp\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tests\TestApp\TestBundle as App;
use function range;
use function sprintf;

/**
 * @Route("/demo-responses")
 */
class DemoResponseController extends BaseController
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
        $entity = new App\Entity\Genre();
        $entity
            ->setId(1)
            ->setSlug('demo-slug');

        return new App\ResponseModel\Genre($entity);
    }

    /**
     * @Route("/collection-of-response-models", methods="GET")
     */
    public function collectionOfResponseModelsAction()
    {
        $result = [];

        foreach (range(1, 3) as $id) {
            $entity = new App\Entity\Genre();
            $entity
                ->setId($id)
                ->setSlug(sprintf('%d-demo-slug', $id));

            $result[] = new App\ResponseModel\Genre($entity);
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
