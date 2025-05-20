<?php

declare(strict_types=1);

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use RestApiBundle\Mapping\Mapper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class UploadBook implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public UploadedFile $file;

    #[Assert\NotBlank]
    public string $title;
}
