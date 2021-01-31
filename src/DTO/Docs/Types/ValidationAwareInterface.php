<?php

namespace RestApiBundle\DTO\Docs\Types;

use Symfony\Component\Validator\Constraint;

interface ValidationAwareInterface
{
    /**
     * @return Constraint[]
     */
    public function getConstraints(): array;

    /**
     * @param Constraint[] $constraints
     */
    public function setConstraints(array $constraints);
}
