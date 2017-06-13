<?php

use Symfony\Component\HttpFoundation\Request;
use forteroche\Domain\Comment;
use forteroche\Domain\Article;
use forteroche\Domain\Chapter;
use forteroche\Form\Type\CommentType;
use forteroche\Form\Type\ArticleType;
use forteroche\Form\Type\ChapterType;

//-----------------------------------------------------------------------------
// Home page / access to all articles
$app->get('/', function() use($app) {
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('index.html.twig', array('articles' => $articles));
})->bind('home');

// Access to an article
$app->match('/article/{id}', function($id, Request $request) use($app) {
    $article = $app['dao.article']->find($id);
    $nbrComments = $app['dao.comment']->countComments($id);
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
    return $app['twig']->render('single.html.twig', array('article' => $article, 'nbrComments' => $nbrComments, 'comments' => $comments, 'commentForm' => $commentFormView));
})->bind('article');

// Access to chapters
$app->get('/chapters', function() use($app) {
    $chapters = $app['dao.chapter']->findAll();
    return $app['twig']->render('chapters.html.twig', array('chapters' => $chapters));
})->bind('chapters');

// Access to about
$app->get('/about', function() use($app) {
    return $app['twig']->render('about.html.twig');
})->bind('about');

// Login form
$app->get('/login', function(Request $request) use($app) {
    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username')
    ));
})->bind('login');


// ADMIN
// Access to admin
$app->get('/admin', function() use($app) {
    $articles = $app['dao.article']->findAll();
    $comments = $app['dao.comment']->findAll();
    $chapters = $app['dao.chapter']->findAll();
    return $app['twig']->render('admin.html.twig', array('articles' => $articles, 'comments' => $comments, 'chapters' => $chapters));
})->bind('admin');

// Article
//-----------------------------------------------------------------------------
// Add a new article
$app->match('/admin/article/add', function(Request $request) use($app) {
    $article = new Article();
    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);
    if($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'Le nouvel article à bien été créé.');
        return $app->redirect($app["url_generator"]->generate("admin"));
    } else {
        return $app['twig']->render('article_form.html.twig', array(
            'title' => 'Créer un nouvel article', 
            'articleForm' => $articleForm->createView(),
        ));
    }
})->bind('admin_article_add');

// Edite an existing article
$app->match('/admin/article/{id}/edit', function($id, Request $request) use($app) {
    $article = $app['dao.article']->find($id);
    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);
    if($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', "L'article à bien été mis à jour.");
        return $app->redirect($app["url_generator"]->generate("admin"));
    } else {
        return $app['twig']->render('article_form.html.twig', array('title' => 'Editer l\'article', 'articleForm' => $articleForm->createView()));
    }
})->bind('admin_article_edit');

// Remove an article
$app->get('/admin/article/{id}/delete', function($id, Request $request) use ($app) {
    $app['dao.comment']->deleteAllByArticle($id);
    $app['dao.article']->delete($id);
    $app['session']->getFlashBag()->add('success', 'L\'article à bien été supprimé.');
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_article_delete');

// Comment
//-----------------------------------------------------------------------------
// Edit an existing comment
$app->match('/admin/comment/{id}/edit', function($id, Request $request) use($app) {
    $comment = $app['dao.comment']->find($id);
    $commentForm = $app['form.factory']->create(CommentType::class, $comment);
    $commentForm->handleRequest($request);
    if($commentForm->isSubmitted() && $commentForm->isValid()) {
        $app['dao.comment']->save($comment);
        $app['session']->getFlashBag()->add('success', 'Le commentaire à bien été modifié.');
        return $app->redirect($app["url_generator"]->generate("admin"));
    } else {
        return $app['twig']->render('comment_form.html.twig', array('title' => 'Editer un commentaire', 'commentForm' => $commentForm->createView()));
    }
})->bind('admin_comment_edit');

// Remove a comment
$app->get('/admin/comment/{id}/delete', function($id, Request $request) use($app) {
    $app['dao.comment']->delete($id);
    $app['session']->getFlashbag()->add('success', 'Le commentaire à bien été supprimé.');
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_comment_delete');

// Chapter
//-----------------------------------------------------------------------------
// Add a new chapitre
$app->match('/admin/chapter/add', function(Request $request) use($app) {
    $chapter = new Chapter();
    $chapterForm = $app['form.factory']->create(ChapterType::class, $chapter);
    $chapterForm->handleRequest($request);
    if($chapterForm->isSubmitted() && $chapterForm->isValid()) {
        $app['dao.chapter']->save($chapter);
        $app['session']->getFlashBag()->add('success', 'Le nouvel chapter à bien été créé.');
        return $app->redirect($app["url_generator"]->generate("admin"));
    } else {
        return $app['twig']->render('chapter_form.html.twig', array('title' => 'Créer un nouveau chapitre', 'chapterForm' => $chapterForm->createView()));
    }
})->bind('admin_chapter_add');

// Edite an existing chapter
$app->match('/admin/chapter/{id}/edit', function($id, Request $request) use($app) {
    $chapter = $app['dao.chapter']->find($id);
    $chapterForm = $app['form.factory']->create(ChapterType::class, $chapter);
    $chapterForm->handleRequest($request);
    if($chapterForm->isSubmitted() && $chapterForm->isValid()) {
        $app['dao.chapter']->save($chapter);
        $app['session']->getFlashBag()->add('success', "Le chapitre à bien été mis à jour.");
        return $app->redirect($app["url_generator"]->generate("admin"));
    } else {
        return $app['twig']->render('chapter_form.html.twig', array('title' => 'Editer le chapitre', 'chapterForm' => $chapterForm->createView()));
    }
})->bind('admin_chapter_edit');

// Remove an chapter
$app->get('/admin/chapter/{id}/delete', function($id, Request $request) use ($app) {
    $app['dao.chapter']->delete($id);
    $app['session']->getFlashBag()->add('success', 'Le chapitre à bien été supprimé.');
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_chapter_delete');

// TOOL
//-----------------------------------------------------------------------------
// hash password tool
$app->get('/hashpwd', function() use ($app) {
    $rawPassword = '@dm1n';
    $salt = '%qUgq3NAYfC1MKwrW?yevbE';
    $encoder = $app['security.encoder.bcrypt'];
    return $encoder->encodePassword($rawPassword, $salt);
});

// Global var
$app->before(function (Request $request) use ($app) {
    $app['twig']->addGlobal('current_uri', $request->getRequestUri());
});