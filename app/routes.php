<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 16/05/2017
 * Time: 11:12
 */

// Home page
$app->get('/', function () use ($app) {

    $posts = $app['dao.post']->find(0, 10);
    $blogInfo = $app['dao.blog']->find();

    return $app['twig']->render('index.html.twig', array('posts' => $posts,
                                                         'blog' => $blogInfo));
})->bind('home');