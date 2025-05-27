<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

use Doctrine;

class DoctrineHelper
{
    public static function isEntity(string $class): bool
    {
        $reflectionClass = ReflectionHelper::getReflectionClass($class);

        return (bool) AnnotationReader::getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }
}
