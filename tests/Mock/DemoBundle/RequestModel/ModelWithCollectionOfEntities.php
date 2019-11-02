<?php

namespace Tests\Mock\DemoBundle\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithCollectionOfEntities implements RequestModelInterface
{
    /**
     * @var \Tests\Mock\DemoBundle\Entity\File[]
     *
     * @Mapper\EntitiesCollection(class="Tests\Mock\DemoBundle\Entity\File")
     */
    private $fieldWithCollectionOfEntities;

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
