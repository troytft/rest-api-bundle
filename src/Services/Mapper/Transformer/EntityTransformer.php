<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

use function sprintf;

class EntityTransformer implements TransformerInterface
{
    public const CLASS_OPTION = 'class';
    public const FIELD_OPTION = 'field';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value, array $options)
    {
        $class = $options[static::CLASS_OPTION];
        $field = $options[static::FIELD_OPTION];

        $fieldType = $this->entityManager->getClassMetadata($class)->getTypeOfField($field);

        if ($fieldType === null) {
            throw new \InvalidArgumentException(sprintf('Class "%s" has not a field with name "%s"', $class, $field));
        } elseif ($fieldType === \Doctrine\DBAL\Types\Type::INTEGER) {
            $transformer = new IntegerTransformer();
            $value = $transformer->transform($value);
        } elseif ($fieldType === \Doctrine\DBAL\Types\Type::STRING) {
            $transformer = new StringTransformer();
            $value = $transformer->transform($value);
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported field type "%s"', $fieldType));
        }

        /** @var EntityRepository $repository */
        $repository = $this->entityManager->getRepository($options[static::CLASS_OPTION]);
        $entity = $repository->findOneBy([$options[static::FIELD_OPTION] => $value]);

        if (!$entity) {
            throw new RestApiBundle\Exception\RequestModel\EntityNotFoundException();
        }

        return $entity;
    }
}
