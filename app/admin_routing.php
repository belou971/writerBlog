<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 09/06/2017
 * Time: 14:12
 */

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app->post('/signup-check', function(Request $request) use ($app) {
    $blogInfo = $app['dao.blog']->find();

    $encoder = $app['security.default_encoder'];

    $countRow = $app['dao.admin']->updateAdmin($request, $encoder);

    $msg = $countRow . ' row(s) updated!\n';

    if ($countRow > 0) {
        $response = new Response($msg, 201);
    } else {
        $response = new Response($msg, 404);
    }
    return $response;
})->bind('signup-check');

$app->get('/signup', function(Request $request) use ($app) {

    $blogInfo = $app['dao.blog']->find();
    return $app['twig']->render('sign-up.html.twig', array('blog' => $blogInfo)
    );
})->bind('signup');

// Connexion form page
$app->get('/connexion', function(Request $request) use ($app) {

    $blogInfo = $app['dao.blog']->find();

    return $app['twig']->render('connexion.html.twig', array('blog'  => $blogInfo,
                                                             'error' => $app['security.last_error']($request),
                                                             'last_username' => $app['session']->get('_security.last_username'),
    ));

})->bind('connexion');


// Administration home page
$app->get('/admin/', function() use ($app) {
    $blogInfo = $app['dao.blog']->find();

    return $app['twig']->render('post-admin.html.twig', array('blog'  => $blogInfo));

})->bind('admin_home');


// Adminstration: New post form page
$app->get('/admin/new-post', function() use ($app) {
    $blogInfo = $app['dao.blog']->find();

    return $app['twig']->render('form-new-post.html.twig', array('blog'  => $blogInfo));

})->bind('new_post_form');

// Administration: Post Edition Form page
$app->get('/admin/edit-post', function() use ($app) {
    $blogInfo = $app['dao.blog']->find();

    return $app['twig']->render('post-overview.html.twig', array('blog'  => $blogInfo));

})->bind('edit_post_form');