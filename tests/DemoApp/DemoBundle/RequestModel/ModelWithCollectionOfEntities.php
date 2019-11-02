<?php

namespace Tests\DemoApp\DemoBundle\RequestModel;

use Tests;
use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithCollectionOfEntities implements RequestModelInterface
{
    /**
     * @var \Tests\DemoApp\DemoBundle\Entity\File[]
     *
     * @Mapper\EntitiesCollection(class="Tests\DemoApp\DemoBundle\Entity\File")
     */
    private $fieldWithCollectionOfEntities;

    /**
     * @return Tests\DemoApp\DemoBundle\Entity\File[]
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
