<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;
use Symfony\Component\PropertyInfo;
use Doctrine\ORM\EntityManagerInterface;

class EntityTransformer implements TransformerInterface
{
    public const CLASS_OPTION = 'class';
    public const FIELD_OPTION = 'field';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RestApiBundle\Services\Mapper\Transformer\StringTransformer $stringTransformer,
        private RestApiBundle\Services\Mapper\Transformer\IntegerTransformer $integerTransformer,
    ) {
    }

    public function transform($value, array $options)
    {
        $class = $options[static::CLASS_OPTION];
        $field = $options[static::FIELD_OPTION];

        $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($class, $field);
        $value = match ($columnType) {
            PropertyInfo\Type::BUILTIN_TYPE_INT => $this->integerTransformer->transform($value),
            PropertyInfo\Type::BUILTIN_TYPE_STRING => $this->stringTransformer->transform($value),
            default => throw new \InvalidArgumentException(),
        };

        $entity = $this->entityManager->getRepository($class)->findOneBy([$field => $value]);
        if (!$entity) {
            throw new RestApiBundle\Exception\RequestModel\EntityNotFoundException();
        }

        return $entity;
    }
}
