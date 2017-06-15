<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 16/05/2017
 * Time: 11:01
 */

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1'
));

/*$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../log/trace.log',
    'monolog.level' => \Monolog\Logger::DEBUG
));*/

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/admin',
            'form'   => array('login_path'  => '/connexion'    , 'check_path'         => '/admin/login_check'),
            'logout' => array('logout_path' => '/admin/logout' , 'invalidate_session' => true),
            'users'  => function () use ($app) {
                return new writerBlog\DAO\AdminDAO($app['db']);
            },
        ),
    ),
));



// Register services.
$app['dao.post'] = function ($app) {
    return new writerBlog\DAO\PostDAO($app['db']);
};

$app['dao.blog'] = function ($app) {
    return new writerBlog\DAO\BlogDAO($app['db']);
};

$app['dao.category'] = function($app) {
    return new writerBlog\DAO\CategoryDAO($app['db']);
};

$app['dao.admin'] = function ($app) {
    return new writerBlog\DAO\AdminDAO($app['db']);
};

$app['dao.comment'] = function ($app) {
    return new \writerBlog\DAO\CommentDAO($app['db']);
};

$app['dao.subscriber'] = function ($app) {
    return new \writerBlog\DAO\SubscriberDAO($app['db']);
};