<?php

namespace Tests\DemoApp\DemoBundle\RequestModel;

use Tests;
use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithCollectionOfEntities implements RequestModelInterface
{
    /**
     * @var \Tests\DemoApp\DemoBundle\Entity\Genre[]
     *
     * @Mapper\EntitiesCollection(class="Tests\DemoApp\DemoBundle\Entity\Genre")
     */
    private $fieldWithCollectionOfEntities;

    /**
     * @return Tests\DemoApp\DemoBundle\Entity\Genre[]
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
