<?php

class RequestModelResolverTest extends Tests\BaseTestCase
{
    public function testNestedModelWithConstraints()
    {
        $schema = $this->getRequestModelResolver()->resolveByClass(TestApp\RequestModel\ModelWithValidation::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema->getSerializableData()));
    }

    public function testModelWithEntityType()
    {
        $schema = $this->getRequestModelResolver()->resolveByClass(TestApp\RequestModel\ModelWithEntityBySlug::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema->getSerializableData()));
    }

    public function testModelWithArrayOfEntitiesType()
    {
        $schema = $this->getRequestModelResolver()->resolveByClass(TestApp\RequestModel\ModelWithArrayOfEntities::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema->getSerializableData()));
    }

    private function getRequestModelResolver(): RestApiBundle\Services\Docs\OpenApi\RequestModelResolver
    {
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\OpenApi\RequestModelResolver::class);
        if (!$result instanceof RestApiBundle\Services\Docs\OpenApi\RequestModelResolver) {
            throw new \InvalidArgumentException();
        }

        return $result;
    }
}
