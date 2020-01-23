<?php

namespace RestApiBundle\Services\Docs;

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

    public function __construct(
        RestApiBundle\Services\Docs\DocBlockHelper $docBlockHelper,
        RestApiBundle\Services\Docs\TypeHintHelper $typeHintHelper
    ) {
        $this->docBlockHelper = $docBlockHelper;
        $this->typeHintHelper = $typeHintHelper;
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
        $result = $this->docBlockHelper->getReturnTypeByReturnTag($reflectionParameter);
        if (!$result) {
            $result = $this->typeHintHelper->getReturnTypeByReflectionMethod($reflectionParameter);
        }

        return $result;
    }
}
