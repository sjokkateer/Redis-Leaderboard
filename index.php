<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\LeaderBoardController;

$controller = new LeaderBoardController();
echo $controller->index();
