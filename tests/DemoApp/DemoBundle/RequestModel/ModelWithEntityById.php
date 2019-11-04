<?php

namespace Tests\DemoApp\DemoBundle\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntityById implements RequestModelInterface
{
    /**
     * @var \Tests\DemoApp\DemoBundle\Entity\Genre
     *
     * @Mapper\Entity(class="\Tests\DemoApp\DemoBundle\Entity\Genre")
     */
    private $fieldWithEntity;

    public function getFieldWithEntity(): \Tests\DemoApp\DemoBundle\Entity\Genre
    {
        return $this->fieldWithEntity;
    }

    public function setFieldWithEntity(\Tests\DemoApp\DemoBundle\Entity\Genre $fieldWithEntity)
    {
        $this->fieldWithEntity = $fieldWithEntity;

        return $this;
    }
}
