<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

interface DescriptionAwareInterface
{
    public function getDescription(): ?string;
    public function setDescription(?string $description);
}
