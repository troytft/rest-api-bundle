<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class EnumTransformer implements TransformerInterface
{
    public const CLASS_OPTION = 'class';

    public function transform($value, array $options)
    {
        $class = $options[static::CLASS_OPTION] ?? throw new \InvalidArgumentException();
        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData($class);

        if (\is_string($value) && $enumData->type === PropertyInfo\Type::BUILTIN_TYPE_INT) {
            $value = (int) $value;
        }

        if (!\in_array($value, $enumData->values, true)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\ValueNotFoundInEnumException();
        }

        $result = \call_user_func([$class, 'from'], $value);
        if ($result === false) {
            throw new \LogicException();
        }

        return $result;
    }
}
