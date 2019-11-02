<?php

namespace Tests\Mock\DemoBundle\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntity implements RequestModelInterface
{
    /**
     * @var \Tests\Mock\DemoBundle\Entity\File
     *
     * @Mapper\Entity(class="\Tests\Mock\DemoBundle\Entity\File")
     */
    private $fieldWithEntity;

    public function getFieldWithEntity(): \Tests\Mock\DemoBundle\Entity\File
    {
        return $this->fieldWithEntity;
    }

    public function setFieldWithEntity(\Tests\Mock\DemoBundle\Entity\File $fieldWithEntity)
    {
        $this->fieldWithEntity = $fieldWithEntity;

        return $this;
    }
}
