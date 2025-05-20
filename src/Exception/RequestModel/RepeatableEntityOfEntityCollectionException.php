<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\RequestModel;

use RestApiBundle\Exception\Mapper\Transformer\TransformerExceptionInterface;

class RepeatableEntityOfEntityCollectionException extends \Exception implements TransformerExceptionInterface
{
}
