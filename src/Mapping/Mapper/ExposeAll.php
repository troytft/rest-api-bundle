<?php

declare(strict_types=1);

namespace RestApiBundle\Mapping\Mapper;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ExposeAll
{
}
