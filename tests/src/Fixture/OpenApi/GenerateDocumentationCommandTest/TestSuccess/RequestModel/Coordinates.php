<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Coordinates implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public float $latitude;

    public float $longitude;
}
