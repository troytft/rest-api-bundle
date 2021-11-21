<?php

class RequestModelResolverTest extends Tests\BaseTestCase
{
    public function testNestedModelWithConstraints()
    {
        $schema = $this->getRequestModelResolver()->toSchema(TestApp\RequestModel\ModelWithValidation::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema->getSerializableData()));
    }

    public function testModelWithEntityType()
    {
        $schema = $this->getRequestModelResolver()->toSchema(TestApp\RequestModel\ModelWithEntityBySlug::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema->getSerializableData()));
    }

    public function testModelWithArrayOfEntitiesType()
    {
        $schema = $this->getRequestModelResolver()->toSchema(TestApp\RequestModel\ModelWithArrayOfEntities::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema->getSerializableData()));
    }

    private function getRequestModelResolver(): RestApiBundle\Services\OpenApi\Specification\RequestModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\Specification\RequestModelResolver::class);
    }
}
