<?php

namespace RestApiBundle\Exception\RequestModel;

use Mapper\Exception\Transformer\TransformerExceptionInterface;

class OneEntityOfEntitiesCollectionNotFoundException extends \Exception implements TransformerExceptionInterface
{
}
