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
        $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($class, $fieldName);
        $value = match ($columnType) {
            PropertyInfo\Type::BUILTIN_TYPE_INT => $this->integerTransformer->transform($value),
            PropertyInfo\Type::BUILTIN_TYPE_STRING => $this->stringTransformer->transform($value),
            default => throw new \InvalidArgumentException(),
        };

        $entity = $this->entityManager->getRepository($class)->findOneBy([$fieldName => $value]);
        if (!$entity) {
            throw new RestApiBundle\Exception\RequestModel\EntityNotFoundException();
        }

        return $entity;
    }

    private function transformMultipleItems(string $class, string $fieldName, mixed $value): array
    {
        $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($class, $fieldName);
        if ($columnType === PropertyInfo\Type::BUILTIN_TYPE_INT && !\is_array($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfIntegersRequiredException();
        } elseif ($columnType === PropertyInfo\Type::BUILTIN_TYPE_STRING && !\is_array($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfStringsRequiredException();
        }

        if (!\count($value)) {
            return [];
        }

        $firstCollectionItem = $value[0] ?? null;
        if ($columnType === PropertyInfo\Type::BUILTIN_TYPE_INT && !is_numeric($firstCollectionItem)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfIntegersRequiredException();
        } elseif ($columnType === PropertyInfo\Type::BUILTIN_TYPE_STRING && (!\is_string($firstCollectionItem) && !is_numeric($firstCollectionItem))) {
            throw new RestApiBundle\Exception\Mapper\Transformer\CollectionOfStringsRequiredException();
        }

        if (\count($value) !== \count(array_unique($value))) {
            throw new RestApiBundle\Exception\RequestModel\RepeatableEntityOfEntityCollectionException();
        }

        $results = $this->entityManager->getRepository($class)->findBy([$fieldName => $value]);
        if (\count($results) !== \count($value)) {
            throw new RestApiBundle\Exception\RequestModel\OneEntityOfEntitiesCollectionNotFoundException();
        }

        $sortedResults = [];
        $getterName = 'get'.ucfirst($fieldName);

        foreach ($results as $object) {
            if (!method_exists($object, $getterName)) {
                throw new \InvalidArgumentException();
            }

            $key = array_search($object->{$getterName}(), $value, true);
            if ($key === false) {
                throw new \InvalidArgumentException();
            }

            $sortedResults[$key] = $object;
        }

        unset($results);
        ksort($sortedResults);

        return $sortedResults;
    }
}
