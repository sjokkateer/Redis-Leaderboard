<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\LeaderBoardController;
use Predis\Connection\ConnectionException;

try {
    $controller = new LeaderBoardController();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $method = array_keys($_POST)[0];
        $output = $controller->determineAction($method);
    } else {
        $output = $controller->index();
    }

    echo $output;
} catch (ConnectionException $e) {
    echo "Failed to connect to the redis server";
    echo '<br/>';
    echo $e->getMessage();
}
