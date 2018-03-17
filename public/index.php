<?php

declare(strict_types=1);

use Project\Code\Actions;


require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App();

$app->get('/',        Actions::class . ':index');
$app->get('/phpinfo', Actions::class . ':info');

$app->run();
