<?php

namespace Tests\Fixture\OpenApi\SchemaGeneratorTest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends AbstractController
{
    public function redirectResponseAction(): RedirectResponse
    {
        return new RedirectResponse('');
    }

    public function binaryFileResponseAction(): BinaryFileResponse
    {
        return new BinaryFileResponse(new File('', false));
    }

    public function voidResponseAction(): void
    {
    }
}
