<?php

namespace RestApiBundle\Exception\Mapper\Transformer;

use RestApiBundle;

class WrappedTransformerException extends \Exception implements RestApiBundle\Exception\Mapper\StackableMappingExceptionInterface
{
    use RestApiBundle\Exception\Mapper\PathTrait;

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
