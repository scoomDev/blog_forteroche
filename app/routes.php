<?php

use Symfony\Component\HttpFoundation\Request;
use forteroche\Domain\Comment;
use forteroche\Form\Type\CommentType;

// Home page
$app->get('/', function() use($app) {
    $url = $_SERVER['REQUEST_URI'];
    return $app['twig']->render('index.html.twig', array('url' => $url));
})->bind('home');

// Access to all articles
$app->get('/articles', function() use($app) {
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('articles.html.twig', array('articles' => $articles));
})->bind('articles');

// Access to an article
$app->match('/article/{id}', function($id, Request $request) use($app) {
    $article = $app['dao.article']->find($id);
    $commentFormView = null;
    $comment = new Comment();
    $comment->setArticle($article);
    $commentForm = $app['form.factory']->create(CommentType::class, $comment);
    $commentForm->handleRequest($request);
    if($commentForm->isSubmitted() && $commentForm->isValid()) {
        $app['dao.comment']->save($comment);
        $app['session']->getFlashBag()->add('success', 'Votre commentaire à bien été pris en compte');
    }
    $commentFormView = $commentForm->createView();
    $comments = $app['dao.comment']->findAllByArticle($id);
    return $app['twig']->render('single.html.twig', array('article' => $article, 'comments' => $comments, 'commentForm' => $commentFormView));
})->bind('article');

// Access to chapters
$app->get('/chapters', function() use($app) {
    return $app['twig']->render('chapters.html.twig');
})->bind('chapters');

// Login form
$app->get('/login', function(Request $request) use($app) {
    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username')
    ));
})->bind('login');

// hash password tool
$app->get('/hashpwd', function() use ($app) {
    $rawPassword = '@dm1n';
    $salt = '%qUgq3NAYfC1MKwrW?yevbE';
    $encoder = $app['security.encoder.bcrypt'];
    return $encoder->encodePassword($rawPassword, $salt);
});