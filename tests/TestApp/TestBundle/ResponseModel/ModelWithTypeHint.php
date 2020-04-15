<?php

namespace Tests\TestApp\TestBundle\ResponseModel;

use RestApiBundle;

class ModelWithTypeHint implements RestApiBundle\ResponseModelInterface
{
    public function getStringField(): string
    {
        return 'string';
    }

    public function getNullableStringField(): ?string
    {
        return null;
    }

    public function getDateTimeField(): \DateTime
    {
        return new \DateTime();
    }
}
