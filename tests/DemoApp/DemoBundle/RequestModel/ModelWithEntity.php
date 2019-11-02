<?php

namespace Tests\DemoApp\DemoBundle\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntity implements RequestModelInterface
{
    /**
     * @var \Tests\DemoApp\DemoBundle\Entity\File
     *
     * @Mapper\Entity(class="\Tests\DemoApp\DemoBundle\Entity\File")
     */
    private $fieldWithEntity;

    public function getFieldWithEntity(): \Tests\DemoApp\DemoBundle\Entity\File
    {
        return $this->fieldWithEntity;
    }

    public function setFieldWithEntity(\Tests\DemoApp\DemoBundle\Entity\File $fieldWithEntity)
    {
        $this->fieldWithEntity = $fieldWithEntity;

        return $this;
    }
}
