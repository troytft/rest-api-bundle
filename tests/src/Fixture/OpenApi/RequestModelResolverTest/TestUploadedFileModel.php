<?php

namespace Tests\Fixture\OpenApi\RequestModelResolverTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestUploadedFileModel implements Mapper\ModelInterface
{
    public \Symfony\Component\HttpFoundation\File\UploadedFile $file;

    public ?string $previewName;
}
