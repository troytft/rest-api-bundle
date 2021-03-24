<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithArrayOfEntities implements RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Book[]
     *
     * @Mapper\ArrayOfEntitiesType(class="TestApp\Entity\Book")
     */
    private $genres;

    /**
     * @return TestApp\Entity\Book[]
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    public function setGenres(array $genres)
    {
        $this->genres = $genres;

        return $this;
    }
}
