<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\RequestModel;

use RestApiBundle\Exception\Mapper\Transformer\TransformerExceptionInterface;

class EntityNotFoundException extends \Exception implements TransformerExceptionInterface
{
}
