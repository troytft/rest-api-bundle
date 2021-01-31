<?php

namespace RestApiBundle\DTO\OpenApi\Types;

interface DescriptionAwareInterface
{
    public function getDescription(): ?string;
    public function setDescription(?string $description);
}
