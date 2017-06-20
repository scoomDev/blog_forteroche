
<?php
// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => 'localhost',
    'port' => '3306',
    'dbname' => 'blog_forteroche',
    'user' => 'root',
    'password' => '',
);


$app['image_directory'] = 'http://forteroche/img';

// define log parameters
$app['monolog.level'] = 'WARNING';
