<?php

namespace RestApiBundle\Exception\Mapper\Transformer;

use Mapper\Exception\StackableMappingExceptionInterface;
use RestApiBundle\Exception\Mapper\PathTrait;

class WrappedTransformerException extends \Exception implements StackableMappingExceptionInterface
{
    use PathTrait;

    /**
     * @var TransformerExceptionInterface
     */
    private $transformerException;

    public function __construct(TransformerExceptionInterface $transformerException, array $path)
    {
        $this->transformerException = $transformerException;
        $this->path = $path;

        parent::__construct($transformerException->getMessage(), 0, $transformerException);
    }

    public function getTransformerException(): TransformerExceptionInterface
    {
        return $this->transformerException;
    }
}
