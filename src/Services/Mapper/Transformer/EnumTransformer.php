<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

class EnumTransformer implements TransformerInterface
{
    public const CLASS_OPTION = 'class';

    public function transform($value, array $options)
    {
        $class = $options[static::CLASS_OPTION] ?? throw new \InvalidArgumentException();
        $enumValues = RestApiBundle\Helper\TypeExtractor::extractEnumValues($class);

        if (!in_array($value, $enumValues, true)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\ValueNotFoundInEnumException();
        }

        $result = call_user_func([$class, 'from'], $value);
        if ($result === false) {
            throw new \LogicException();
        }

        return $result;
    }
}
