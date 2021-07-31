<?php

namespace Tests\Fixture\Mapper;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class Movie implements Mapper\ModelInterface
{
    /**
     * @Mapper\AutoType
     */
    public string $name = 'Taxi 2';

    /**
     * @Mapper\AutoType
     */
    public float $rating = 4.7;

    /**
     * @var int|null
     *
     * @Mapper\AutoType
     */
    private ?int $lengthMinutes = null;

    /**
     * @var bool|null
     *
     * @Mapper\AutoType
     */
    private ?bool $isOnlineWatchAvailable = null;

    /**
     * @var string[]|null
     *
     * @Mapper\AutoType
     */
    private ?array $genres = null;

    /**
     * @var Tests\Fixture\Mapper\Release[]|null
     *
     * @Mapper\AutoType
     */
    private ?array $releases = null;

    /**
     * @return string[]|null
     */
    public function getGenres(): ?array
    {
        return $this->genres;
    }

    /**
     * @param string[]|null $genres
     *
     * @return $this
     */
    public function setGenres(?array $genres)
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * @return Tests\Fixture\Mapper\Release[]|null
     */
    public function getReleases(): ?array
    {
        return $this->releases;
    }

    /**
     * @param Tests\Fixture\Mapper\Release[]|null $releases
     *
     * @return $this
     */
    public function setReleases(?array $releases)
    {
        $this->releases = $releases;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLengthMinutes(): ?int
    {
        return $this->lengthMinutes;
    }

    /**
     * @param int|null $lengthMinutes
     *
     * @return $this
     */
    public function setLengthMinutes(?int $lengthMinutes)
    {
        $this->lengthMinutes = $lengthMinutes;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsOnlineWatchAvailable(): ?bool
    {
        return $this->isOnlineWatchAvailable;
    }

    /**
     * @param bool|null $isOnlineWatchAvailable
     *
     * @return $this
     */
    public function setIsOnlineWatchAvailable(?bool $isOnlineWatchAvailable)
    {
        $this->isOnlineWatchAvailable = $isOnlineWatchAvailable;

        return $this;
    }
}
