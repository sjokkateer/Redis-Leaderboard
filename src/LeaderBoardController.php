<?php


namespace App;


class LeaderBoardController
{
    private PlayerManager $playerManager;

    public function __construct() {
       $this->playerManager = new PlayerManager();
       $this->playerManager->initializeApp();
    }

    public function index(): string
    {
        $players = $this->playerManager->getAllPlayers();
        // Sort players by rating descending.
        usort($players, fn($p1, $p2) => -1 * ($p1->getPerformanceRating() - $p2->getPerformanceRating()));

        $output = $this->loadTemplate('players', ['players' => $players]);
        return $this->loadTemplate('index', ['output' => $output]);
    }

    public function simulateGame(): string
    {
        // First get two random players which should be a method on the player manager class.
        $playerOne = $this->playerManager->getRandomPlayer();

        do{
            $playerTwo = $this->playerManager->getRandomPlayer();
        } while ($playerTwo->getId() == $playerOne->getId());

        $this->playerManager->simulateGame($playerOne, $playerTwo);

        return $this->index();
    }

    private function loadTemplate(string $template, array $data = []): string
    {
        extract($data);

        ob_start();
        include __DIR__ . "/../templates/$template.html.php";
        return ob_get_clean();
    }
}