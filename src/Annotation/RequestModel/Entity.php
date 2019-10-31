<?php

namespace RestApiBundle\Annotation\RequestModel;

use Mapper\Annotation\NullableTrait;
use Mapper\DTO\Mapping\ScalarTypeInterface;
use Mapper\Transformer\BooleanTransformer;
use Mapper\Transformer\DateTransformer;
use RestApiBundle\Manager\RequestModel\EntityTransformer;
use function is_string;

/**
 * @Annotation
 */
class Entity implements ScalarTypeInterface
{
    use NullableTrait;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $field = 'id';

    public function getTransformer(): string
    {
        return EntityTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [
            EntityTransformer::CLASS_OPTION => $this->class,
            EntityTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
