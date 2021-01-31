<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithCollectionOfEntities implements RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Genre[]
     *
     * @Mapper\ArrayOfEntitiesType(class="TestApp\Entity\Genre")
     */
    private $fieldWithCollectionOfEntities;

    /**
     * @return TestApp\Entity\Genre[]
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
