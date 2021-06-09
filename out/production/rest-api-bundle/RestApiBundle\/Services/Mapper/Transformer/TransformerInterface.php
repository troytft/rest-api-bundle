<?php

namespace RestApiBundle\Services\Mapper\Transformer;

interface TransformerInterface
{
    public function transform($value, array $options);
}
