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

    public function simulateGame(): void
    {
        $playerOne = $this->playerManager->getRandomPlayer();

        do{
            $playerTwo = $this->playerManager->getRandomPlayer();
        } while ($playerTwo->getId() == $playerOne->getId());

        $this->playerManager->simulateGame($playerOne, $playerTwo);
        $this->redirectHome();
    }

    private function redirectHome(): void
    {
        // Redirect to index page for display
        header('Location: ' . $_SERVER['PHP_SELF']);
        die;
    }

    public function clearCache(): void
    {
        $this->playerManager->clearCache();
        $this->redirectHome();
    }

    public function determineAction(string $method): void
    {
        switch($method) {
            case 'clear':
                $this->clearCache();
                break;
            case 'simulate':
                $this->simulateGame();
                break;
            default:
                $this->index();
                break;
        }
    }

    private function loadTemplate(string $template, array $data = []): string
    {
        extract($data);

        ob_start();
        include __DIR__ . "/../templates/$template.html.php";
        return ob_get_clean();
    }
}