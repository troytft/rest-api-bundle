<?php

namespace RestApiBundle\Exception\OpenApi;

interface ContextAwareExceptionInterface extends \Throwable
{
    public function getContext(): string;
}
