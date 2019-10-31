<?php

namespace RestApiBundle\Manager\RequestModel;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Mapper\Transformer\TransformerInterface;
use RestApiBundle\Exception\RequestModel\EntityNotFoundException;

class EntityTransformer implements TransformerInterface
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
        /** @var EntityRepository $repository */
        $repository = $this->entityManager->getRepository($options[static::CLASS_OPTION]);
        $entity = $repository->findOneBy([$options[static::FIELD_OPTION] => $value]);

        if (!$entity) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }
}
