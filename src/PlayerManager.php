<?php

declare(strict_types=1);

namespace App;

use Predis\Client;

class PlayerManager
{
    private const SET_OF_PLAYERS = "players";
    private const K = 32;

    public const NUMBER_OF_TOTAL_PLAYERS = 10;

    private Client $redis;
    private array $players;

    public function __construct()
    {
        $this->redis = new Client();
        $this->players = [];
    }

    public function clearCache(): void
    {
        $this->redis->flushAll();
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getPlayerPerformanceRating(Player $player)
    {
        return $this->redis->get($player->getId());
    }

    public function addPlayer(Player $player): void
    {
        // This should be refactored to adding the player id
        // to the cache's 
        array_push($this->players, $player);
        $this->addPerformanceRatingToCache($player);

        $this->redis->hset(self::SET_OF_PLAYERS, $player->getId(), $player->getPerformanceRating());
    }

    // Returns the ratings after game is played [playerOneNewRating, $playerTwoNewRating]
    public function simulateGame(Player $playerOne, Player $playerTwo): array
    {
        $playerOneProbabilityOfWinning = $this->calculateProbabilityOfWinning($playerOne, $playerTwo);
        $potentialRatingChange = self::K * (1 - $playerOneProbabilityOfWinning);

        $playerOneWon = $this->determineIfPlayerWon($playerOneProbabilityOfWinning);
        $playerOneRatingChange = $playerOneWon ? $potentialRatingChange : -1 * $potentialRatingChange;
        $playerTwoRatingChange = -1 * $playerOneRatingChange;

        $this->updateRatingForPlayer($playerOne, (int) $playerOneRatingChange);
        $this->updateRatingForPlayer($playerTwo, (int) $playerTwoRatingChange);

        return [
            $this->getPlayerPerformanceRating($playerOne),
            $this->getPlayerPerformanceRating($playerTwo)
        ];
    }

    private function calculateProbabilityOfWinning(Player $playerOne, Player $playerTwo): float
    {
        // Calculate transformed rating for both.
        $playerOneTransformedRating = $this->getTransformedRating($playerOne);
        $playerTwoTransformedRating = $this->getTransformedRating($playerTwo);

        return $playerOneTransformedRating / ($playerOneTransformedRating + $playerTwoTransformedRating);
    }

    private function getTransformedRating(Player $player): float
    {
        $currentPerformanceRating = $this->getPlayerPerformanceRating($player);
        $exp =  $currentPerformanceRating / 400.0;

        return pow(10, $exp);
    }

    private function determineIfPlayerWon(float $probabilityOfWinning): bool
    {
        return lcg_value() <= $probabilityOfWinning;
    }

    private function updateRatingForPlayer(Player $player, int $ratingChange): void
    {
        $absoluteRatingChange = abs($ratingChange);

        if ($ratingChange < 0) {
            $this->redis->decrby($player->getId(), $absoluteRatingChange);
        } else {
            $this->redis->incrby($player->getId(), $absoluteRatingChange);
        }
    }

    private function addPerformanceRatingToCache(Player $player): void
    {
        $this->redis->set($player->getId(), $player->getPerformanceRating());
    }

    public function initializeApp(): void
    {
        // Get the id's out of the cache (set)
        $players = $this->redis->hgetall(self::SET_OF_PLAYERS);

        // $players can be empty, then we initialize the app with some random players.
        if (empty($players)) {
            $this->generateRandomPlayers();
        }

        // Else we should set the maximum player id in the cache to the player static id field to
        // continue create new players formally. (for experiment/demonstration not too relevant)
    }

    private function generateRandomPlayers(): void
    {
        for ($i = 0; $i < self::NUMBER_OF_TOTAL_PLAYERS; $i++) {
            $this->addPlayer(Player::generateRandomPlayer());
        }
    }

    public function getAllPlayers(): array
    {
        $players = $this->redis->hgetall(self::SET_OF_PLAYERS);
        $playerObjectCollection = [];

        foreach ($players as $id => $rating) {
            // Redis returns string values only, even though we passed it integers.
            array_push($playerObjectCollection, Player::create((int) $id, (int) $rating));
        }

        return $playerObjectCollection;
    }
}
