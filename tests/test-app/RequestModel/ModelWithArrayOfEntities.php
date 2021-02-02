<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithArrayOfEntities implements RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Genre[]
     *
     * @Mapper\ArrayOfEntitiesType(class="TestApp\Entity\Genre")
     */
    private $genres;

    /**
     * @return TestApp\Entity\Genre[]
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
