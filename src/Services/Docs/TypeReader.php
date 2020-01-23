<?php

namespace RestApiBundle\Services\Docs;

use Doctrine\ORM\EntityManagerInterface;
use RestApiBundle;

class TypeReader
{
    /**
     * @var RestApiBundle\Services\Docs\DocBlockHelper
     */
    private $docBlockHelper;

    /**
     * @var RestApiBundle\Services\Docs\TypeHintHelper
     */
    private $typeHintHelper;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        RestApiBundle\Services\Docs\DocBlockHelper $docBlockHelper,
        RestApiBundle\Services\Docs\TypeHintHelper $typeHintHelper,
        EntityManagerInterface $entityManager
    ) {
        $this->docBlockHelper = $docBlockHelper;
        $this->typeHintHelper = $typeHintHelper;
        $this->entityManager = $entityManager;
    }

    public function getReturnTypeByReflectionMethod(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        $result = $this->docBlockHelper->getReturnTypeByReturnTag($reflectionMethod);
        if (!$result) {
            $result = $this->typeHintHelper->getReturnTypeByReflectionMethod($reflectionMethod);
        }

        return $result;
    }

    public function getTypeByReflectionParameter(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->getTypeFromString((string) $reflectionParameter->getType(), $reflectionParameter->allowsNull());
    }

    public function getTypeFromString(string $value, bool $isNullable): RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        switch ($value) {
            case 'string':
                $result = new RestApiBundle\DTO\Docs\Type\StringType($isNullable);

                break;

            case 'int':
            case 'integer':
                $result = new RestApiBundle\DTO\Docs\Type\IntegerType($isNullable);

                break;

            case 'float':
                $result = new RestApiBundle\DTO\Docs\Type\FloatType($isNullable);

                break;

            case 'bool':
            case 'boolean':
                $result = new RestApiBundle\DTO\Docs\Type\BooleanType($isNullable);

                break;

            default:
                $result = new RestApiBundle\DTO\Docs\Type\UnknownClassType($value, $isNullable);
        }

        return $result;
    }
}
