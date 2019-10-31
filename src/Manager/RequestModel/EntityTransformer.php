<?php

namespace RestApiBundle\Manager\RequestModel;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Mapper\Transformer\TransformerInterface;
use RestApiBundle\Exception\RequestModel\EntityNotFoundException;
use Tests\Mock\DemoBundle\Repository\FileRepository;
use function get_class;
use function is_bool;
use Mapper\Exception\Transformer\BooleanRequiredException;
use function var_dump;

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
//
//        $fieldType = $this->em->getClassMetadata($this->entityName)->getTypeOfField($this->fieldName);
//        if ($fieldType === \Doctrine\DBAL\Types\Type::INTEGER && !is_numeric($value)) {
//            throw new ValidationFieldException($this->getPropertyName(), 'Значение должно быть числом');
//        } elseif (!is_numeric($value) && !is_string($value)) {
//            throw new ValidationFieldException($this->getPropertyName(), 'Значение должно быть строкой или числом');
//        }
//        $entity = $this->em->getRepository($this->entityName)->findOneBy([$this->fieldName => $value]);
//        if (!$entity && !$this->nullable) {
//            throw new ValidationFieldException($this->getPropertyName(), 'Сущность с таким значением не найдена');
//        }
//        return $entity;
    }
}
