<?php

namespace Tests\DemoApp\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tests\DemoApp\DemoBundle as App;
use RestApiBundle\Annotation\Docs;

class DemoController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Registration")
     *
     * @Route("/register", methods="POST")
     *
     * @param App\RequestModel\ModelWithValidation $model
     *
     * @return Response
     */
    public function registerAction(App\RequestModel\ModelWithValidation $model): Response
    {
        return new Response('ok');
    }
}
