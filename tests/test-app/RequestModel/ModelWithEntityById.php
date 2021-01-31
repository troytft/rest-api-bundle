<?php

namespace TestApp\RequestModel;

use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntityById implements RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Genre
     *
     * @Mapper\EntityType(class="\TestApp\Entity\Genre")
     */
    private $fieldWithEntity;

    public function getFieldWithEntity(): \TestApp\Entity\Genre
    {
        return $this->fieldWithEntity;
    }

    public function setFieldWithEntity(\TestApp\Entity\Genre $fieldWithEntity)
    {
        $this->fieldWithEntity = $fieldWithEntity;

        return $this;
    }
}
