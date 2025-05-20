<?php declare(strict_types=1);

class FileTypeResolverTest extends Tests\BaseTestCase
{
    public function testSuccess(): void
    {
        $this->assertSame(RestApiBundle\Helper\OpenApi\FileTypeResolver::JSON_TYPE, RestApiBundle\Helper\OpenApi\FileTypeResolver::resolveByFilename('file.json'));
        $this->assertSame(RestApiBundle\Helper\OpenApi\FileTypeResolver::YAML_TYPE, RestApiBundle\Helper\OpenApi\FileTypeResolver::resolveByFilename('file.yaml'));
        $this->assertSame(RestApiBundle\Helper\OpenApi\FileTypeResolver::YAML_TYPE, RestApiBundle\Helper\OpenApi\FileTypeResolver::resolveByFilename('file.yml'));
    }

    public function testInvalid(): void
    {
        try {
            RestApiBundle\Helper\OpenApi\FileTypeResolver::resolveByFilename('file.xml');
        } catch (\Exception $exception) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        }

        try {
            RestApiBundle\Helper\OpenApi\FileTypeResolver::resolveByFilename('file_without_extension');
        } catch (\Exception $exception) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        }
    }
}
