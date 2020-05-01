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

    public function getPlayerPerformanceRating(Player $player): ?string
    {
        return $this->redis->hget(self::SET_OF_PLAYERS, $player->getId());
    }

    public function addPlayer(Player $player): void
    {
        $this->redis->hset(self::SET_OF_PLAYERS, $player->getId(), $player->getPerformanceRating());
    }

    // Returns the ratings after game is played [playerOneNewRating, $playerTwoNewRating]
    public function simulateGame(Player $playerOne, Player $playerTwo): array
    {
        $playerOneProbabilityOfWinning = $this->calculateProbabilityOfWinning($playerOne, $playerTwo);
        $potentialRatingChangePlayerOne = self::K * (1 - $playerOneProbabilityOfWinning);

        // Calculate the potential point change for player two as well.
        $playerTwoProbabilityOfWinning = $this->calculateProbabilityOfWinning($playerTwo, $playerOne);
        $potentialRatingChangePlayerTwo = self::K * (1 - $playerTwoProbabilityOfWinning);

        $playerOneWon = $this->determineIfPlayerWon($playerOneProbabilityOfWinning);
        // In case player one lost, it should lose the points that player two could win.
        $playerOneRatingChange = $playerOneWon ? $potentialRatingChangePlayerOne : -1 * $potentialRatingChangePlayerTwo;
        // Player two should increment by the opposite.
        $playerTwoRatingChange = -1 * $playerOneRatingChange;

        // On a set there is no decrease but increasing a positive number with a negative number still works.
        $this->redis->hincrby(self::SET_OF_PLAYERS, $playerOne->getId(), (int) round($playerOneRatingChange));
        $this->redis->hincrby(self::SET_OF_PLAYERS, $playerTwo->getId(), (int) round($playerTwoRatingChange));

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

    public function initializeApp(): void
    {
        // Get the id's out of the cache (set)
        $players = $this->redis->hgetall(self::SET_OF_PLAYERS);

        // $players can be empty, then we initialize the app with some random players.
        if (empty($players)) {
            $this->generateRandomPlayers();
        } else {
            Player::setId($this->getLastPlayerId());
        }
    }

    public function getLastPlayerId(): int
    {
        $players = $this->redis->hgetall(self::SET_OF_PLAYERS);
        $lastId = -1;

        foreach ($players as $id => $rating) {
            if ($lastId < $id) {
                $lastId = $id;
            }
        }

        return $lastId;
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

    public function getRandomPlayer(): ?Player
    {
        $players = $this->getAllPlayers();
        return count($players) > 0 ? $players[rand(0, count($players) - 1)] : null;
    }
}
