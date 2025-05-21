<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

use Doctrine;
use RestApiBundle;
use Symfony\Component\PropertyInfo;

class DoctrineHelper
{
    public static function isEntity(string $class): bool
    {
        $reflectionClass = ReflectionHelper::getReflectionClass($class);

        return (bool) AnnotationReader::getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }
}
