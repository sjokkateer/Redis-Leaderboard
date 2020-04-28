<?php

use App\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    public function test_constructor_playerConstructedWithDefaultConstructor_expectedDefaultRating()
    {
        // Arrange
        $player = new Player();

        // Act
        $actualRating = $player->getPerformanceRating();
        $expectedRating = Player::DEFAULT_RATING;

        // Assert
        $this->assertEquals($expectedRating, $actualRating, "player rating should default to: $expectedRating but was $actualRating!");
    }

    public function test_constructor_playerConstructedWithRandomIntegerValueLargerThan1000_expectedRatingToEqualRandomlyGeneratedValue()
    {
        // Arrange
        $randomPerformanceRating = Player::getRandomValidPlayerRating();
        $player = new Player($randomPerformanceRating);

        // Act
        $actualRating = $player->getPerformanceRating();
        $expectedRating = $randomPerformanceRating;

        // Assert
        $this->assertEquals($expectedRating, $actualRating, "player rating should be equal to the random int generated: $expectedRating but was $actualRating");
    }

    public function test_constructor_playerConstructedWithRandomIntegerValueLessThanPlayerMinRating_expectedRatingToEqualMinRating()
    {
        // Arrange
        $randomPerformanceRating = rand(PHP_INT_MIN, Player::MIN_RATING - 1);
        $player = new Player($randomPerformanceRating);

        // Act
        $actualRating = $player->getPerformanceRating();
        $expectedRating = Player::MIN_RATING;

        // Assert
        $this->assertEquals($expectedRating, $actualRating, "player rating should be equal to the min rating: $expectedRating but was $actualRating");
    }

    /**
     * Because all functions are executed in ¿order? there were already 3 ids assigned to
     * player objects and the first player created in this method actually resulted in an id of 4.
     *
     * @return void
     */
    public function test_constructor_playerConstructedWithDefaultConstructorOrWithout_ShouldHaveAUniqueIdAssigned()
    {
        // Arrange
        $firstPlayer = $this->constructPlayerRandomly();
        $followingPlayer = $this->constructPlayerRandomly();

        // Act
        $expectedFollowingPlayerId = $firstPlayer->getId() + 1;
        $actualFollowingPlayerId = $followingPlayer->getId();

        // Assert 
        $this->assertEquals($expectedFollowingPlayerId, $actualFollowingPlayerId, "following player should have an id 1 larger than the first player but resulted in: $actualFollowingPlayerId");
    }

    /**
     * Helper method that randomly generates a player object
     * either through default construction or by passing a
     * random start rating into it.
     *
     * @return Player
     */
    private function constructPlayerRandomly(): Player
    {
        if (rand(0, 1)) {
            return Player::generateRandomPlayer();
        }

        return new Player();
    }
}
