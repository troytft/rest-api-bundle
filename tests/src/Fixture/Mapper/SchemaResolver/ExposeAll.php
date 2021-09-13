<?php

namespace Tests\Fixture\Mapper\SchemaResolver;

use RestApiBundle\Mapping\Mapper as Mapper;

#[Mapper\ExposeAll]
class ExposeAll implements Mapper\ModelInterface
{
    private string $field1;

    private \DateTime $field2;

    #[Mapper\DateFormat('d/m/Y')]
    private Mapper\DateInterface $field3;

    public function getField1(): string
    {
        return $this->field1;
    }

    public function setField1(string $field1): static
    {
        $this->field1 = $field1;

        return $this;
    }

    public function getField2(): \DateTime
    {
        return $this->field2;
    }

    public function setField2(\DateTime $field2): static
    {
        $this->field2 = $field2;

        return $this;
    }

    public function getField3(): Mapper\DateInterface
    {
        return $this->field3;
    }

    public function setField3(Mapper\DateInterface $field3): static
    {
        $this->field3 = $field3;

        return $this;
    }
}
