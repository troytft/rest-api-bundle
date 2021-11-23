<?php

namespace Tests\Fixture\OpenApi;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AllResponsesController extends AbstractController
{
    public function redirectResponseAction(): RedirectResponse
    {
    }

    public function binaryFileResponseAction(): BinaryFileResponse
    {
    }

    public function voidResponseAction(): void
    {
    }
}
