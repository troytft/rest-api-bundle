<?php

declare(strict_types=1);

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;
use Tests;

#[Mapper\ExposeAll]
class WriterList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public int $offset;

    public int $limit;

    #[Assert\Length(min: 1, max: 255)]
    public ?string $name;

    #[Assert\Length(min: 1, max: 255)]
    public ?string $surname;

    public ?Mapper\Date $birthday;

    /**
     * @deprecated
     *
     * @var Tests\Fixture\TestApp\Entity\Book[]|null
     */
    public ?array $genres;
}
