<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class EnumTransformer implements TransformerInterface
{
    public const CLASS_OPTION = 'class';
    public const BACKING_TYPE_OPTION = 'backingType';

    public function __construct(
        private StringTransformer $stringTransformer,
        private IntegerTransformer $integerTransformer,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function transform(mixed $value, array $options): mixed
    {
        $class = $options[static::CLASS_OPTION] ?? throw new \InvalidArgumentException();
        $backingType = $options[static::BACKING_TYPE_OPTION] ?? throw new \InvalidArgumentException();

        $value = match ($backingType) {
            PropertyInfo\Type::BUILTIN_TYPE_INT => $this->integerTransformer->transform($value),
            PropertyInfo\Type::BUILTIN_TYPE_STRING => $this->stringTransformer->transform($value),
            default => throw new \InvalidArgumentException(),
        };

        $result = $class::tryFrom($value);
        if (!$result) {
            throw new RestApiBundle\Exception\Mapper\Transformer\ValueNotFoundInEnumException();
        }

        return $result;
    }
}
