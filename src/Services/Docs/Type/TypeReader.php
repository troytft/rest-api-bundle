<?php

namespace RestApiBundle\Services\Docs\Type;

use Doctrine\ORM\EntityManagerInterface;
use RestApiBundle;

class TypeReader
{
    /**
     * @var RestApiBundle\Services\Docs\Type\DocBlockHelper
     */
    private $docBlockHelper;

    /**
     * @var RestApiBundle\Services\Docs\Type\TypeHintHelper
     */
    private $typeHintHelper;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RestApiBundle\Services\Docs\ResponseModelHelper
     */
    private $responseModelHelper;

    public function __construct(
        RestApiBundle\Services\Docs\Type\DocBlockHelper $docBlockHelper,
        RestApiBundle\Services\Docs\Type\TypeHintHelper $typeHintHelper,
        EntityManagerInterface $entityManager,
        RestApiBundle\Services\Docs\ResponseModelHelper $responseModelHelper
    ) {
        $this->docBlockHelper = $docBlockHelper;
        $this->typeHintHelper = $typeHintHelper;
        $this->entityManager = $entityManager;
        $this->responseModelHelper = $responseModelHelper;
    }

    public function getReturnTypeByReflectionMethod(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        $result = $this->docBlockHelper->getReturnTypeByReturnTag($reflectionMethod);
        if (!$result) {
            $result = $this->typeHintHelper->getReturnTypeByReflectionMethod($reflectionMethod);
        }

        if ($result instanceof RestApiBundle\DTO\Docs\Type\ClassType || $result instanceof RestApiBundle\DTO\Docs\Type\ClassesCollectionType) {
            if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($result->getClass())) {
                throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
            }

            $objectType = $this->responseModelHelper->getObjectTypeByClass($result->getClass(), $result->getIsNullable());

            if ($result instanceof RestApiBundle\DTO\Docs\Type\ClassesCollectionType) {
                $result = new RestApiBundle\DTO\Docs\Type\CollectionType($objectType, $result->getIsNullable());
            } else {
                $result = $objectType;
            }
        }

        return $result;
    }

    public function getTypeByReflectionParameter(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        return $this->typeHintHelper->getParameterTypeByReflectionParameter($reflectionParameter);
    }
}
