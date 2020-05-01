<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\LeaderBoardController;

$controller = new LeaderBoardController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $output = $controller->simulateGame();
} else {
    $output = $controller->index();
}

echo $output;
