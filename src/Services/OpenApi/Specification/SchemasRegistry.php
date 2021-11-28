<?php

namespace RestApiBundle\Services\OpenApi\Specification;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;

class SchemasRegistry
{
    /**
     * @return array<string, OpenApi\Schema>
     */
    public function dumpSchemas(): array
    {
        $result = [];

        foreach ($this->typenameCache as $class => $typename) {
            $result[$typename] = $this->schemaCache[$class];
        }

        ksort($result);

        return $result;
    }
}