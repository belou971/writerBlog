<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 09/06/2017
 * Time: 14:12
 */

use \Symfony\Component\HttpFoundation\Request;

// connexion form
$app->get('/connexion', function(Request $request) use ($app) {
    /*return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));*/
    return null;
})->bind('connexion');