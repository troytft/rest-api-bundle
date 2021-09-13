<?php

namespace RestApiBundle\Mapping\Mapper;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Expose
{
}
