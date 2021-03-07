<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

interface AttributesExtractorInterface
{
    /**
     * @return string[]
     */
    public function extractByClass(string $class): array;

    /**
     * @return string[]
     */
    public function extractByInstance(RestApiBundle\ResponseModelInterface $instance): array;
}
