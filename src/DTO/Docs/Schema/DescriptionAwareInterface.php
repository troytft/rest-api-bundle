<?php

namespace RestApiBundle\DTO\Docs\Schema;

use Symfony\Component\Validator\Constraint;

interface DescriptionAwareInterface
{
    public function getDescription(): ?string;
    public function setDescription(?string $description);
}
