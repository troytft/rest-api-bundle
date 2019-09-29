<?php

namespace Tests\Demo\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;
use Tests\Demo\Entity\File;

class CreateMovie implements RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapper\StringType()
     */
    private $name;

    /**
     * @var File|null
     *
     * @Mapper\Entity(class="File", nullable=true)
     */
    private $image;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image)
    {
        $this->image = $image;

        return $this;
    }
}
