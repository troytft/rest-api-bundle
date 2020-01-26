<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use cebe\openapi\SpecObjectInterface;
use Symfony\Component\Yaml\Yaml;
use function file_put_contents;

class SchemaFileWriter
{
    public function writeToYamlFile(SpecObjectInterface $object, string $fileName)
    {
        $data = Yaml::dump($object->getSerializableData(), 256, 4, Yaml::DUMP_OBJECT_AS_MAP);

        if (file_put_contents($fileName, $data) === false) {
            throw new \RuntimeException();
        }
    }
}
