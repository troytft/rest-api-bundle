<?php

declare(strict_types=1);

namespace RestApiBundle\Services\OpenApi;

use cebe\openapi\spec as OpenApi;
use RestApiBundle;

class EnumSchemaRegistry
{
    /**
     * @var array<string, OpenApi\Schema>
     */
    private array $schemaCache = [];

    /**
     * @var array<string, string>
     */
    private array $typenameCache = [];

    /**
     * @return OpenApi\Schema|OpenApi\Reference
     */
    public function resolveReference(string $enumClass, bool $nullable)
    {
        $typename = $this->typenameCache[$enumClass] ?? null;
        if (!$typename) {
            $typename = RestApiBundle\Helper\TypenameResolver::resolve($enumClass, 'Enum');
            $classInCache = \array_search($typename, $this->typenameCache, true);
            if ($classInCache !== false && $classInCache !== $enumClass) {
                throw new \InvalidArgumentException(\sprintf('Typename %s for enum class %s already used by another class %s', $typename, $enumClass, $classInCache));
            }

            $this->typenameCache[$enumClass] = $typename;
            $this->schemaCache[$enumClass] = $this->createEnumSchema($enumClass);
        }

        $reference = new OpenApi\Reference([
            '$ref' => \sprintf('#/components/schemas/%s', $typename),
        ]);

        if ($nullable) {
            return new OpenApi\Schema([
                'anyOf' => [
                    $reference,
                    new OpenApi\Schema(['type' => 'null']),
                ],
            ]);
        }

        return $reference;
    }

    /**
     * @return array<string, OpenApi\Schema>
     */
    public function dumpSchemas(): array
    {
        $result = [];

        foreach ($this->typenameCache as $class => $typename) {
            $result[$typename] = $this->schemaCache[$class];
        }

        \ksort($result);

        return $result;
    }

    private function createEnumSchema(string $enumClass): OpenApi\Schema
    {
        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData($enumClass);

        return new OpenApi\Schema([
            'type' => $enumData->type === \Symfony\Component\PropertyInfo\Type::BUILTIN_TYPE_STRING
                ? OpenApi\Type::STRING
                : OpenApi\Type::INTEGER,
            'nullable' => false,
            'enum' => $enumData->values,
        ]);
    }
}
