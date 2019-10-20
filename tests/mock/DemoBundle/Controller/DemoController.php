<?php

namespace Tests\Mock\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Tests\Mock\DemoBundle as App;
use function var_dump;

class DemoController extends BaseController
{
    /**
     * @Route("/register", methods="POST")
     */
    public function registerAction(App\RequestModel\ModelWithValidation $model)
    {
        var_dump('sss');die();
    }
}
