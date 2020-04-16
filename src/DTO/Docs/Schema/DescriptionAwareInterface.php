<?php

namespace RestApiBundle\DTO\Docs\Schema;

interface DescriptionAwareInterface
{
    public function getDescription(): ?string;
    public function setDescription(?string $description);
}
