<?php
declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper;

use function join;

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
        return join('.', $this->path);
    }
}
