<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper;

trait PathTrait
{
    /**
     * @var array
     */
    protected $path;

    public function getPath(): array
    {
        return $this->path;
    }

    public function getPathAsString(): string
    {
        return \implode('.', $this->path);
    }
}
