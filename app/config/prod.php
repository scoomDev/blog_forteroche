<?php
// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => 'db687410862.db.1and1.com',
    'port' => '3306',
    'dbname' => 'db687410862',
    'user' => 'dbo687410862',
    'password' => 'chris24051985Ad',
);

$app['image_directory'] = 'http://forteroche.scoomdev.eu/img';

// define log parameters
$app['monolog.level'] = 'WARNING';
