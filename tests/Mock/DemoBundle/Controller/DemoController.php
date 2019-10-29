<?php

namespace Tests\Mock\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tests\Mock\DemoBundle as App;

class DemoController extends BaseController
{
    /**
     * @Route("/register", methods="POST")
     */
    public function registerAction(App\RequestModel\ModelWithValidation $model)
    {
        return new Response('ok');
    }
}
