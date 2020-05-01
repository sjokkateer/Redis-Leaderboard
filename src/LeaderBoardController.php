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
        usort($players, fn($p1, $p2) => -1 * ($p1->getPerformanceRating() - $p2->getPerformanceRating()));

        $output = $this->loadTemplate('players', ['players' => $players]);
        return $this->loadTemplate('index', ['output' => $output]);
    }

    public function simulateGame(): string
    {
        $playerOne = $this->playerManager->getRandomPlayer();

        do{
            $playerTwo = $this->playerManager->getRandomPlayer();
        } while ($playerTwo->getId() == $playerOne->getId());

        $this->playerManager->simulateGame($playerOne, $playerTwo);

        // Redirect to index page for display
        header('Location: ' . $_SERVER['PHP_SELF']);
        die;
    }

    private function loadTemplate(string $template, array $data = []): string
    {
        extract($data);

        ob_start();
        include __DIR__ . "/../templates/$template.html.php";
        return ob_get_clean();
    }
}