<?php

use App\Player;
use App\PlayerManager;
use PHPUnit\Framework\TestCase;

class PlayerManagerTest extends TestCase
{
    private const PLAYER_ONE = 0;
    private const PLAYER_TWO = 1;

    private PlayerManager $playerManager;
    // Actually the cache should be cleared after every run of tests.

    protected function setUp(): void
    {
        $this->playerManager = new PlayerManager();
    }

    protected function tearDown(): void
    {
        $this->playerManager->clearCache();
    }

    // On construction we should have a connection to redis.
    public function test_constructor_constructingWithDefaultConstructor_expectedConnectionEstablishedAndArrayInitialized(): void
    {
        // Arrange
        // Act
        $actualCollectionOfPlayers = $this->playerManager->getPlayers();
        $expectedCollectionOfPlayers = [];

        // Assert
        $this->assertEquals($expectedCollectionOfPlayers, $actualCollectionOfPlayers, "expected an empty array of players after constructing new player manager object");
    }

    public function test_getPlayerRating_playerNotInCache_expectedNullReturned(): void
    {
        // Arrange
        $player = new Player();

        // Act
        $expectedPlayerPerformanceRating = $this->playerManager->getPlayerPerformanceRating($player);

        // Assert
        $this->assertNull($expectedPlayerPerformanceRating, "player with id does not exist in cache and should return no value, but returned $expectedPlayerPerformanceRating");
    }

    public function test_getPlayerRating_playerInCache_expectedPerformanceRatingReturnedEqualToPlayerRating(): void
    {
        // Arrange
        $player = new Player();
        $this->playerManager->addPlayer($player);

        // Act
        $actualPlayerPerformanceRating = $this->playerManager->getPlayerPerformanceRating($player);
        $expectedPlayerPerformanceRating = Player::DEFAULT_RATING;

        // Assert
        $this->assertEquals($expectedPlayerPerformanceRating, $actualPlayerPerformanceRating, "player should be in cache with default performance rating ($expectedPlayerPerformanceRating), but got: $actualPlayerPerformanceRating");
    }

    public function test_updatePlayerPerformanceRatings_randomPlayerOfTwoWins_playerWhoWonGotIncreaseInRatingAndPlayerWhoLostDecreaseByEqualAmounts()
    {
        // Arrange
        $playerOne = new Player();
        $playerTwo = new Player();

        $this->playerManager->addPlayer($playerOne);
        $this->playerManager->addPlayer($playerTwo);

        // Act
        $playerOnePerformanceRatingBeforeGame = $this->playerManager->getPlayerPerformanceRating($playerOne);
        $playerTwoPerformanceRatingBeforeGame = $this->playerManager->getPlayerPerformanceRating($playerTwo);

        $result = $this->playerManager->simulateGame($playerOne, $playerTwo);

        $playerOneRatingAfterSimulation = $result[self::PLAYER_ONE];
        $playerTwoRatingAfterSimulation = $result[self::PLAYER_TWO];

        // Assert
        $message = 'ratings should be different after a simulation is played!';
        $this->assertNotEquals($playerOnePerformanceRatingBeforeGame, $playerOneRatingAfterSimulation, $message);
        $this->assertNotEquals($playerTwoPerformanceRatingBeforeGame, $playerTwoRatingAfterSimulation, $message);
        $this->assertEquals(abs($playerOnePerformanceRatingBeforeGame - $playerOneRatingAfterSimulation), abs($playerTwoPerformanceRatingBeforeGame - $playerTwoRatingAfterSimulation), 'There can be no difference in the total change of rating!');
    }

    public function test_initializePlayerManager_noPlayersInCache_applicationInitializedWithTenRandomPlayers()
    {
        // Arrange
        $this->playerManager->initializeApp();

        // Act
        $playerCollection = $this->playerManager->getAllPlayers();

        $expectedNumberOfPlayersInCollection = PlayerManager::NUMBER_OF_TOTAL_PLAYERS;
        $actualNumberOfPlayersInCollections = count($playerCollection);

        // Assert
        $this->assertEquals($expectedNumberOfPlayersInCollection, $actualNumberOfPlayersInCollections, "number of expected players $expectedNumberOfPlayersInCollection did not match actual $actualNumberOfPlayersInCollections");
    }
}
