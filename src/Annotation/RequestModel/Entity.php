<?php

namespace RestApiBundle\Annotation\RequestModel;

/**
 * @Annotation
 */
class Entity extends \Mapper\Annotation\StringType
{
    /**
     * @var string|null
     */
    public $class;
}
