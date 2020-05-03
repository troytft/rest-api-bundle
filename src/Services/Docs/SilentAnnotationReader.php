<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;

class SilentAnnotationReader
{
    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
    }

    public function getClassAnnotation(\ReflectionClass $class, $annotationName)
    {
        try {
            $result = $this->annotationReader->getClassAnnotation($class, $annotationName);
        } catch (AnnotationException $exception) {
            $result = null;
        }

        return $result;
    }

    public function getPropertyAnnotation(\ReflectionProperty $property, $annotationName)
    {
        try {
            $result = $this->annotationReader->getPropertyAnnotation($property, $annotationName);
        } catch (AnnotationException $exception) {
            $result = null;
        }

        return $result;
    }

    public function getMethodAnnotation(\ReflectionMethod $method, $annotationName)
    {
        try {
            $result = $this->annotationReader->getMethodAnnotation($method, $annotationName);
        } catch (AnnotationException $exception) {
            $result = null;
        }

        return $result;
    }
}
