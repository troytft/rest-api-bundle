<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class WriterData implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    #[Assert\Length(min: 1, max: 255)]
    public string $name;

    #[Assert\Length(min: 1, max: 255)]
    public string $surname;

    public ?Mapper\Date $birthday;

    /**
     * @var Tests\Fixture\TestApp\Entity\Genre[]
     */
    public array $genres;
}
