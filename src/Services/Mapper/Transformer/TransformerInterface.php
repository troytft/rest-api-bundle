<?php declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

interface TransformerInterface
{
    public function transform($value, array $options);
}
