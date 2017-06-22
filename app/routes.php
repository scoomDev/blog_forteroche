<?php

use Symfony\Component\HttpFoundation\Request;
use forteroche\Domain\Comment;
use forteroche\Domain\Article;
use forteroche\Domain\Chapter;
use forteroche\Form\Type\CommentType;
use forteroche\Form\Type\ArticleType;
use forteroche\Form\Type\ChapterType;
use forteroche\Form\Type\HeaderType;

//-----------------------------------------------------------------------------
// Home page
$app->get('/', function() use($app) {
    $header = $app['dao.header']->find();
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('index.html.twig', array('articles' => $articles, 'header' => $header));
})->bind('home');

// Access to an article
$app->match('/article/{id}', function($id, Request $request) use($app) {
    $article = $app['dao.article']->find($id);
    $commentFormView = null;
    $comment = new Comment();
    $comment->setArticle($article);
    $nbrComments = $app['dao.comment']->countComments($article->getid());
    $commentForm = $app['form.factory']->create(CommentType::class, $comment);
    $commentForm->handleRequest($request);
    if($commentForm->isSubmitted() && $commentForm->isValid()) {
        $app['dao.comment']->save($comment);
        $app['session']->getFlashBag()->add('success', 'Votre commentaire à bien été pris en compte');
    }
    $commentFormView = $commentForm->createView();
    $comments = $app['dao.comment']->findAllByArticle($id);
    return $app['twig']->render('single.html.twig', array('article' => $article, 'comments' => $comments, 'nbrComments' => $nbrComments, 'commentForm' => $commentFormView));
})->bind('article');

// Access to chapters
$app->get('/chapters', function(Request $request) use($app) {
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

// Recovery form
$app->match('/login/recovery', function(Request $request) use($app) {
    if(isset($_POST['recovery_submit'], $_POST['recovery_mail'])) {
        if(!empty($_POST['recovery_mail'])) {
            $recovery_mail = htmlspecialchars($_POST['recovery_mail']);
            if(filter_var($recovery_mail, FILTER_VALIDATE_EMAIL)) {
                $mailexist = $app['dao.user']->mailExist($recovery_mail);
                if($mailexist == 1) {
                    $app['session']->set('recovery_mail', ['mail' => $recovery_mail]);
                    $recovery_code = "";
                    for ($i=0; $i < 8; $i++) { 
                        $recovery_code .= mt_rand(1,9);
                    }
                    $app['dao.user']->insertCode($recovery_mail, $recovery_code);
                    $pseudo = $app['dao.user']->recoveryFind($recovery_mail);
                    $pseudo = $pseudo['usr_name'];
                    include '../views/inc/mail.php';
                    mail($recovery_mail, "Récupération de mot de passe", $message, $header);
                    $app['session']->getFlashBag()->add('success', 'Un mail contenant le code de confirmation viens de vous être adressé.');
                    return $app->redirect($app["url_generator"]->generate("recovery.confirm"));
                } else {
                    $app['session']->getFlashBag()->add('error', 'Cette adresse mail n\'est pas enregistré.');
                }
            } else {
                $app['session']->getFlashBag()->add('error', 'Veuillez entrer une adresse mail valide.');
            }
        } else {
            $app['session']->getFlashBag()->add('error', 'Veuillez entrer votre adresse mail.');
        }
    }
    return $app['twig']->render('login.html.twig');
})->bind('recovery');

// Confirm with code form
$app->match('/login/recovery/confirm', function(Request $request) use($app) {
    $recovery_mail = $app['session']->get('recovery_mail');
    if(isset($_POST['check_submit'], $_POST['check_code'])) {
        if(!empty($_POST['check_code'])) {
            $check_req = $app['dao.user']->checkValid($recovery_mail['mail'], $_POST['check_code']);
            if($check_req == 1) {
                $app['dao.user']->delRecovery($recovery_mail['mail']);
                return $app->redirect($app["url_generator"]->generate("change.pwd"));
            } else {
                $app['session']->getFlashBag()->add('error', 'Veuillez entrer un code valide.');
            }
        } else {
            $app['session']->getFlashBag()->add('error', 'Veuillez entrer votre code de confirmation');
        }
    }
    return $app['twig']->render('login.html.twig');
})->bind('recovery.confirm');

// Change pwd form
$app->match('/login/change', function(Request $request) use($app) {
    $session = $app['security.token_storage']->getToken()->getUser();
    $recovery_mail = $app['session']->get('recovery_mail');
    if(isset($recovery_mail)) {
        if(isset($_POST['new_pwd_submit'])) {
            if(isset($_POST['new_pwd'], $_POST['new_pwd_confirm'])) {
                $pwd = $_POST['new_pwd'];
                $pwdc = $_POST['new_pwd_confirm'];
                if (!empty($pwd) && !empty($pwdc)) {
                    if ($pwd === $pwdc) {
                        $salt = '%qUgq3NAYfC1MKwrW?yevbE';
                        $encoder = $app['security.encoder.bcrypt'];
                        $hash_pwd = $encoder->encodePassword($pwd, $salt);
                        $app['dao.user']->updatePwd($recovery_mail['mail'], $hash_pwd);
                        $app['session']->getFlashBag()->add('success', 'Votre mot de passe à bien été modifié');
                        return $app->redirect($app["url_generator"]->generate("login"));
                    } else {
                        $app['session']->getFlashBag()->add('error', 'Vos mots de passe ne correspondent pas');
                    }
                } else {
                    $app['session']->getFlashBag()->add('error', 'Veuillez remplir tous les champs');
                }
            } else {
                $app['session']->getFlashBag()->add('error', 'Veuillez remplir tous les champs');
            }
        }
    } else if($session !== 'anon.') {
        if(isset($_POST['new_pwd_submit'])) {
            if(isset($_POST['new_pwd'], $_POST['new_pwd_confirm'])) {
                $pwd = $_POST['new_pwd'];
                $pwdc = $_POST['new_pwd_confirm'];
                if (!empty($pwd) && !empty($pwdc)) {
                    if ($pwd === $pwdc) {
                        $salt = '%qUgq3NAYfC1MKwrW?yevbE';
                        $encoder = $app['security.encoder.bcrypt'];
                        $hash_pwd = $encoder->encodePassword($pwd, $salt);
                        $app['dao.user']->updatePwd($session->getEmail(), $hash_pwd);
                        $app['session']->getFlashBag()->add('success', 'Votre mot de passe à bien été modifié');
                        return $app->redirect($app["url_generator"]->generate("admin"));
                    } else {
                        $app['session']->getFlashBag()->add('error', 'Vos mots de passe ne correspondent pas');
                    }
                } else {
                    $app['session']->getFlashBag()->add('error', 'Veuillez remplir tous les champs');
                }
            } else {
                $app['session']->getFlashBag()->add('error', 'Veuillez remplir tous les champs');
            }
        } else {
            $app['session']->getFlashBag()->add('error', 'Veuillez remplir tous les champs');
        }
    } else {
        $app['session']->getFlashBag()->add('error', 'Veuillez être connecté ou avoir demandé une réinitialisation de votre mot de passe');
        return $app->redirect($app["url_generator"]->generate("login"));
    }
    
return $app['twig']->render('change.html.twig');
})->bind('change.pwd');

// ADMIN
// Access to admin
$app->get('/admin', function() use($app) {
    $articles = $app['dao.article']->findAll();
    $comments = $app['dao.comment']->findAll();
    $chapters = $app['dao.chapter']->findAll();
    $header = $app['dao.header']->find();
    return $app['twig']->render('admin.html.twig', array(
        'articles' => $articles, 
        'comments' => $comments, 
        'chapters' => $chapters, 
        'header' => $header
    ));
})->bind('admin');

// Article
//-----------------------------------------------------------------------------
// Add a new article
$app->match('/admin/article/add', function(Request $request) use($app) {
    $article = new Article();
    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);
    if($articleForm->isSubmitted() && $articleForm->isValid()) {
        if (null !== $article->getImage()) {
            $app['dao.article']->upImg($article);
        }
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'Le nouvel article à bien été créé.');
        return $app->redirect($app["url_generator"]->generate("admin"));
    } else {
        return $app['twig']->render('article_form.html.twig', array(
            'title' => 'Créer un nouvel article', 
            'articleForm' => $articleForm->createView(),
        ));
    }
    return $app['twig']->render('article_form.html.twig', array('title' => 'Créer un nouvel article', 'articleForm' => $articleForm->createView()));
})->bind('admin_article_add');

// Edite an existing article
$app->match('/admin/article/{id}/edit', function($id, Request $request) use($app) {
    $article = $app['dao.article']->find($id);
    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);
    if($articleForm->isSubmitted() && $articleForm->isValid()) {
        if (null !== $article->getImage()) {
            $app['dao.article']->upImg($article);
        }
        $app['dao.article']->update($article);
        $app['session']->getFlashBag()->add('success', "L'article à bien été mis à jour.");
    }
    return $app['twig']->render('article_form.html.twig', array('title' => 'Editer l\'article', 'file_title' => 'Changer l\'image d\'en-tête' , 'article' => $article, 'articleForm' => $articleForm->createView()));
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
    }
    return $app['twig']->render('comment_form.html.twig', array('title' => 'Editer un commentaire', 'commentForm' => $commentForm->createView()));
})->bind('admin_comment_edit');

// Remove a comment
$app->get('/admin/comment/{id}/delete', function($id, Request $request) use($app) {
    $app['dao.comment']->delete($id);
    $app['session']->getFlashbag()->add('success', 'Le commentaire à bien été supprimé.');
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_comment_delete');

// Report a comment
$app->get('/comment/{id}/report', function($id, Request $request) use($app) {
    $comment = $app['dao.comment']->find($id);
    $app['dao.comment']->report($comment);
    $app['session']->getFlashbag()->add('success', 'Le commentaire à bien été signalé.');
    return $app->redirect($app['url_generator']->generate('article', ['id' => $comment->getArticle()->getId()]));
})->bind('comment_report');

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

// header
//-----------------------------------------------------------------------------
// Edite an existing header
$app->match('/admin/header/{id}/edit', function($id, Request $request) use($app) {
    $header = $app['dao.header']->find();
    $headerForm = $app['form.factory']->create(headerType::class, $header);
    $headerForm->handleRequest($request);
    if($headerForm->isSubmitted() && $headerForm->isValid()) {
        if (null !== $headerForm->getData()->getImage1()) {
            $app['dao.header']->upImg1($header);
        }
        if (null !== $headerForm->getData()->getImage2()) {
            $app['dao.header']->upImg2($header);
        }
        $app['dao.header']->update($header);
        $app['session']->getFlashBag()->add('success', "L'en-tête à bien été mis à jour.");
        return $app->redirect($app["url_generator"]->generate("admin"));
    } else {
        return $app['twig']->render('header_form.html.twig', array('title' => 'Editer l\'en-tête', 'headerForm' => $headerForm->createView()));
    }
})->bind('admin_header_edit');

// TOOL
//-----------------------------------------------------------------------------
// hash password tool
$app->get('/hashpwd', function() use ($app) {
    $rawPassword = '24051985';
    $salt = '%qUgq3NAYfC1MKwrW?yevbE';
    $encoder = $app['security.encoder.bcrypt'];
    return $encoder->encodePassword($rawPassword, $salt);
});
