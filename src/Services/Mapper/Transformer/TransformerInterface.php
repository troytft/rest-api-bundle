<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

interface TransformerInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function transform(mixed $value, array $options): mixed;
}
