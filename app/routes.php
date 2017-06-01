<?php

$app->get('/hello/{name}', function($name) use($app) {
    return $app['twig']->render('article.html.twig', array('name' => $name));
});