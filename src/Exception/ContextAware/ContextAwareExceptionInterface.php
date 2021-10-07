<?php

namespace RestApiBundle\Exception\ContextAware;

interface ContextAwareExceptionInterface extends \Throwable
{
    public function getMessageWithContext(): string;
}
