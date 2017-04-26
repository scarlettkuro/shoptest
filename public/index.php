<?php

require __DIR__ . '/../vendor/autoload.php';

use App\App;
use \App\Components\DAO\DAO;

$params = [
    'viewpath' => __DIR__.'/../app/Views/'
];

$app = new App($params);
$app->addComponent('dao', new DAO(parse_ini_file(__DIR__.'/../db.ini')));
$app->addComponent('cart', require(__DIR__ . '/../cart.php'));

$app->addRoutes([
    '/' => [\App\Controllers\Index::class, 'index'],
    '/cart' => [\App\Controllers\Index::class, 'cart'],
    '/add/{id}' => [\App\Controllers\Index::class, 'add'],
    '/remove/{id}' => [\App\Controllers\Index::class, 'remove']
]);

$app->run();