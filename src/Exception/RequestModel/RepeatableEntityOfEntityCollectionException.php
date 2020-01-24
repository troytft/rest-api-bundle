<?php


namespace RestApiBundle\Exception\RequestModel;

use Mapper\Exception\Transformer\TransformerExceptionInterface;

class RepeatableEntityOfEntityCollectionException extends \Exception implements TransformerExceptionInterface
{
}
