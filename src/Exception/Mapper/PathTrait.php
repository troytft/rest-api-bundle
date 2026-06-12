<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper;

trait PathTrait
{
    /**
     * @var array<int|string>
     */
    protected $path;

    /**
     * @return array<int|string>
     */
    public function getPath(): array
    {
        return $this->path;
    }

    public function getPathAsString(): string
    {
        return \implode('.', $this->path);
    }
}
