<?php

namespace RestApiBundle\Exception\RequestModel;

use RestApiBundle\Exception\Mapper\Transformer\TransformerExceptionInterface;

class EntityNotFoundException extends \Exception implements TransformerExceptionInterface
{
}
