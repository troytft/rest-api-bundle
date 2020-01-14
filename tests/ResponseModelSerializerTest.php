<?php

namespace Tests;

use Tests;

class ResponseModelSerializerTest extends BaseBundleTestCase
{
    public function testModelSerialization()
    {
        $entity = new Tests\DemoApp\DemoBundle\Entity\Genre();
        $entity
            ->setId(13)
            ->setSlug('genre-slug');

        $json = $this->getResponseModelSerializer()->toJson(new Tests\DemoApp\DemoBundle\ResponseModel\Genre($entity));

        $this->assertSame('{"id":13,"slug":"genre-slug","__typename":"Genre"}', $json);
    }
}
