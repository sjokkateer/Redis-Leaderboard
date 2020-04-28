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

    public function __construct(int $performanceRating = self::DEFAULT_RATING, int $id = -1)
    {
        $this->playerId = $this->generateId($id);

        $performanceRating = $performanceRating < self::MIN_RATING ? self::MIN_RATING : $performanceRating;
        $this->performanceRating = $performanceRating;
    }

    private function generateId(int $id): int
    {
        if ($id <= 0) {
            $id = self::$id++;
        }

        return $id;
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

    public static function generateRandomPlayer()
    {
        return new Player(self::getRandomValidPlayerRating());
    }

    /**
     * Helper method that generates a random rating
     * between the min rating and max int value.
     *
     * @return integer
     */
    public static function getRandomValidPlayerRating(): int
    {
        return rand(self::MIN_RATING, PHP_INT_MAX);
    }

    public static function create(int $id, int $playerPerformanceRating)
    {
        return new Player($playerPerformanceRating, $id);
    }
}
