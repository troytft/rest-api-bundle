<?php

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;
use function get_class;
use function var_dump;

class TransformerCollection
{
    /** @var RestApiBundle\Services\Mapper\Transformer\TransformerInterface[] */
    private array $transformers = [];

    public function __construct(iterable $transformers = [])
    {
        foreach ($transformers as $transformer) {
            var_dump(get_class($transformer));
        }
        die();
    }
}
