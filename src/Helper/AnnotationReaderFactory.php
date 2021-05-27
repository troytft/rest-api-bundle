<?php

namespace RestApiBundle\Helper;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;

class AnnotationReaderFactory
{
    /**
     * @var AnnotationReader[]
     */
    private static array $cache = [];

    public static function create(bool $ignoreNotImportedAnnotations = false): AnnotationReader
    {
        $key = (int) $ignoreNotImportedAnnotations;

        if (!static::$cache[$key]) {
            $parser = new DocParser();
            $parser
                ->setIgnoreNotImportedAnnotations($ignoreNotImportedAnnotations);

            static::$cache[$key] = new AnnotationReader($parser);
        }

        return static::$cache[$key];
    }
}
