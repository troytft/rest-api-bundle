<?php

namespace Tests\TestApp\TestBundle\RequestModel;

use Tests;
use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithCollectionOfEntities implements RequestModelInterface
{
    /**
     * @var \Tests\TestApp\TestBundle\Entity\Genre[]
     *
     * @Mapper\EntitiesCollection(class="Tests\TestApp\TestBundle\Entity\Genre")
     */
    private $fieldWithCollectionOfEntities;

    /**
     * @return Tests\TestApp\TestBundle\Entity\Genre[]
     */
    public function getFieldWithCollectionOfEntities(): array
    {
        return $this->fieldWithCollectionOfEntities;
    }

    public function setFieldWithCollectionOfEntities(array $fieldWithCollectionOfEntities)
    {
        $this->fieldWithCollectionOfEntities = $fieldWithCollectionOfEntities;

        return $this;
    }
}
