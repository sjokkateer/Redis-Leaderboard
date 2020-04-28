<?php

declare(strict_types=1);

namespace App;

class Player
{
    private static int $id = 1;
    public const MIN_RATING = 1000;
    public const DEFAULT_RATING = 1200;

    private int $playerId;
    private int $performanceRating;

    public function __construct(int $performanceRating = self::DEFAULT_RATING)
    {
        $this->playerId = self::$id++;

        $performanceRating = $performanceRating < self::MIN_RATING ? self::MIN_RATING : $performanceRating;
        $this->performanceRating = $performanceRating;
    }

    public function getPerformanceRating(): int
    {
        return $this->performanceRating;
    }

    public function getId(): int
    {
        return $this->playerId;
    }

    public function __toString(): string
    {
        return "Player(id={$this->playerId}, performance_rating={$this->performanceRating})";
    }
}
