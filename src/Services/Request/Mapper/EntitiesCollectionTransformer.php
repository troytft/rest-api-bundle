<?php

namespace RestApiBundle\Services\Request\Mapper;

use Mapper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use RestApiBundle;
use function count;
use function ucfirst;

class EntitiesCollectionTransformer implements Mapper\Transformer\TransformerInterface
{
    public const CLASS_OPTION = 'class';
    public const FIELD_OPTION = 'field';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value, array $options)
    {
        $class = $options[static::CLASS_OPTION];
        $field = $options[static::FIELD_OPTION];

        if (count($value) !== array_unique($value)) {
            throw new RestApiBundle\Exception\RequestModel\RepeatableEntityOfEntityCollectionException();
        }

        /** @var EntityRepository $repository */
        $repository = $this->entityManager->getRepository($class);
        $results = $repository->findBy([$field => $value]);

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

    public static function getName(): string
    {
        return static::class;
    }
}
