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
     * @Docs\Endpoint(name="Registration")
     *
     * @Route("/register", methods="POST")
     */
    public function registerAction(App\RequestModel\ModelWithValidation $model)
    {
        return new Response('ok');
    }
}
