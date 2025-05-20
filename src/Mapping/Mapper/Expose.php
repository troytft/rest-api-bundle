<?php

declare(strict_types=1);

namespace RestApiBundle\Mapping\Mapper;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Expose
{
}
