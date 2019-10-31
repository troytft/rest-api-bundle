<?php

namespace Tests\Mock\DemoBundle\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntity implements RequestModelInterface
{
    /**
     * @var \Tests\Mock\DemoBundle\Entity\File|null
     *
     * @Mapper\Entity(class="\Tests\Mock\DemoBundle\Entity\File")
     */
    private $fieldWithEntity;

    public function getFieldWithEntity(): bool
    {
        return $this->fieldWithEntity;
    }

    public function setFieldWithEntity(bool $fieldWithEntity)
    {
        $this->fieldWithEntity = $fieldWithEntity;

        return $this;
    }
}
