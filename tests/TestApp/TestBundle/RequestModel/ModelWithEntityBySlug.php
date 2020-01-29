<?php

namespace Tests\TestApp\TestBundle\RequestModel;

use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntityBySlug implements RequestModelInterface
{
    /**
     * @var \Tests\TestApp\TestBundle\Entity\Genre
     *
     * @Mapper\Entity(class="\Tests\TestApp\TestBundle\Entity\Genre", field="slug")
     */
    private $fieldWithEntity;

    public function getFieldWithEntity(): \Tests\TestApp\TestBundle\Entity\Genre
    {
        return $this->fieldWithEntity;
    }

    public function setFieldWithEntity(\Tests\TestApp\TestBundle\Entity\Genre $fieldWithEntity)
    {
        $this->fieldWithEntity = $fieldWithEntity;

        return $this;
    }
}
