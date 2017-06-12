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

// connexion form
$app->get('/connexion', function(Request $request) use ($app) {

    $blogInfo = $app['dao.blog']->find();
    /*return  $app['twig']->render('sign-up.html.twig', array('blog'  => $blogInfo)
    );*/

    //else {
        return $app['twig']->render('connexion.html.twig', array('blog'  => $blogInfo,
                                                                 'error' => $app['security.last_error']($request),
                                                                 'last_username' => $app['session']->get('_security.last_username'),
        ));
    //    return null;
    //}
})->bind('connexion');
