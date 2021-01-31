<?php

namespace RestApiBundle\DTO\Docs\Types;

interface DescriptionAwareInterface
{
    public function getDescription(): ?string;
    public function setDescription(?string $description);
}
