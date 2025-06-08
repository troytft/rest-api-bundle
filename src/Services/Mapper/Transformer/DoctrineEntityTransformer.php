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

    public function transform($value, array $options)
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

    private function transformMultipleItems(string $class, string $fieldName, mixed $value): array
    {
        $propertyType = $this->propertyTypeExtractorService->getTypeRequired($class, $fieldName);
        if ($propertyType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_INT && $propertyType->isCollection()) {
            throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfIntegersRequiredException();
        } elseif ($propertyType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING && $propertyType->isCollection()) {
            throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfStringsRequiredException();
        }

        $firstItem = $value[0] ?? null;

        if ($propertyType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_INT) {
            if (!\is_countable($value)) {
                throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfIntegersRequiredException();
            }

            if (!\count($value)) {
                return [];
            }

            if (!\is_numeric($firstItem)) {
                throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfIntegersRequiredException();
            }

            $value = \array_map(fn ($item) => (int) $item, $value);
        } elseif ($propertyType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING) {
            if (!\is_countable($value)) {
                throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfStringsRequiredException();
            }

            if (!\count($value)) {
                return [];
            }

            if (!\is_string($firstItem) && !\is_numeric($firstItem)) {
                throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfStringsRequiredException();
            }

            $value = \array_map(fn ($item) => (string) $item, $value);
        } else {
            throw new \InvalidArgumentException();
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

        return $sortedResults;
    }
}
