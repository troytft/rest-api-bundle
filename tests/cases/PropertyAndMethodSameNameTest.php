<?php

declare(strict_types=1);

namespace cases;

use RestApiBundle;
use Tests;

class PropertyAndMethodSameNameTest extends Tests\BaseTestCase
{
    public function testOpenApiSchema(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\PropertyAndMethodSameNameTest\Root::class);
        $this->assertMatchesOpenApiSchemaSnapshots($this->getResponseModelResolver()->dumpSchemas());
    }

    private function getResponseModelResolver(): RestApiBundle\Services\OpenApi\ResponseModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\ResponseModelResolver::class);
    }
}
