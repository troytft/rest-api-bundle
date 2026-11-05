<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use Doctrine\ORM\EntityManagerInterface;
use RestApiBundle;
use Symfony\Component\PropertyInfo;

class DoctrineEntityTransformer implements TransformerInterface
{
    public const CLASS_OPTION = 'class';
    public const FIELD_OPTION = 'field';
    public const MULTIPLE_OPTION = 'multiple';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private StringTransformer $stringTransformer,
        private IntegerTransformer $integerTransformer,
        private RestApiBundle\Services\PropertyTypeExtractorService $propertyTypeExtractorService,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function transform(mixed $value, array $options): mixed
    {
        $class = $options[static::CLASS_OPTION] ?? throw new \InvalidArgumentException();
        $fieldName = $options[static::FIELD_OPTION] ?? throw new \InvalidArgumentException();
        $isMultiple = $options[static::MULTIPLE_OPTION] ?? false;

        if ($isMultiple) {
            $result = $this->transformMultipleItems($class, $fieldName, $value);
        } else {
            $result = $this->transformSingleItem($class, $fieldName, $value);
        }

        return $result;
    }

    /**
     * @param class-string $class
     */
    private function transformSingleItem(string $class, string $fieldName, mixed $value): object
    {
        $propertyType = $this->propertyTypeExtractorService->getTypeRequired($class, $fieldName);
        $value = match ($propertyType->getBuiltinType()) {
            PropertyInfo\Type::BUILTIN_TYPE_INT => $this->integerTransformer->transform($value),
            PropertyInfo\Type::BUILTIN_TYPE_STRING => $this->stringTransformer->transform($value),
            default => throw new \InvalidArgumentException(),
        };

        $entity = $this->entityManager->getRepository($class)
            ->findOneBy([$fieldName => $value]);
        if (!$entity) {
            throw new RestApiBundle\Exception\RequestModel\EntityNotFoundException();
        }

        return $entity;
    }

    /**
     * @param class-string $class
     * @param list<int|string> $value
     *
     * @return array<int, object>
     */
    private function transformMultipleItems(string $class, string $fieldName, array $value): array
    {
        if (!$value) {
            return [];
        }

        if (\count($value) !== \count(\array_unique($value))) {
            throw new RestApiBundle\Exception\RequestModel\RepeatableEntityOfEntityCollectionException();
        }

        $results = $this->entityManager->getRepository($class)
            ->findBy([$fieldName => $value]);
        if (\count($results) !== \count($value)) {
            throw new RestApiBundle\Exception\RequestModel\OneEntityOfEntitiesCollectionNotFoundException();
        }

        $sortedResults = [];
        $getterName = 'get' . \ucfirst($fieldName);

        foreach ($results as $object) {
            if (!\method_exists($object, $getterName)) {
                throw new \InvalidArgumentException();
            }

            $key = \array_search($object->{$getterName}(), $value, true);
            if ($key === false) {
                throw new \InvalidArgumentException();
            }

            $sortedResults[$key] = $object;
        }

        unset($results);
        \ksort($sortedResults);

        return \array_values($sortedResults);
    }
}
