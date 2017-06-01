<?php

$app->get('/hello/{name}', function($name) use($app) {
    return $app['twig']->render('article.html.twig', array('name' => $name));
});

$app->get('/', function() use($app) {
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('article.html.twig', array('articles' => $articles));
});