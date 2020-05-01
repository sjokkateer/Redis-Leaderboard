<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\LeaderBoardController;

$controller = new LeaderBoardController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = array_keys($_POST)[0];
    $output = $controller->determineAction($method);
} else {
    $output = $controller->index();
}

echo $output;
