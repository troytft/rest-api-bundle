<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;
use Doctrine\ORM\EntityManagerInterface;

use function count;
use function ucfirst;

class ArrayOfDoctrineEntitiesTransformer implements TransformerInterface
{
    public const CLASS_OPTION = 'class';
    public const FIELD_OPTION = 'field';

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function transform($value, array $options)
    {
        $class = $options[static::CLASS_OPTION] ?? throw new \InvalidArgumentException();
        $field = $options[static::FIELD_OPTION] ?? throw new \InvalidArgumentException();

        if (count($value) !== count(array_unique($value))) {
            throw new RestApiBundle\Exception\RequestModel\RepeatableEntityOfEntityCollectionException();
        }

        $results = $this->entityManager->getRepository($class)->findBy([$field => $value]);
        if (count($results) !== count($value)) {
            throw new RestApiBundle\Exception\RequestModel\OneEntityOfEntitiesCollectionNotFoundException();
        }

        $sortedResults = [];
        $getterName = 'get' . ucfirst($field);

        foreach ($results as $object) {
            if (!method_exists($object, $getterName)) {
                throw new \InvalidArgumentException();
            }

            $key = array_search($object->{$getterName}(), $value);
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
