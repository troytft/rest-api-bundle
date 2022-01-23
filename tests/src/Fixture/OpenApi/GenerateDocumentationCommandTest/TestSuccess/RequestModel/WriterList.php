<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class WriterList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public int $offset;

    public int $limit;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $surname;

    public ?Mapper\Date $birthday;

    /**
     * @var Tests\Fixture\TestApp\Entity\Book[]|null
     */
    public ?array $genres;
}
