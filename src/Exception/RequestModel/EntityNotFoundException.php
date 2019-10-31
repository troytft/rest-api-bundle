<?php

namespace RestApiBundle\Exception\RequestModel;

use Mapper\Exception\Transformer\TransformerExceptionInterface;

class EntityNotFoundException extends \Exception implements TransformerExceptionInterface
{
}
