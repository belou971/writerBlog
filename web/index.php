<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 16/05/2017
 * Time: 12:32
 */
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;

class BlogApplication extends Application {
    use Application\UrlGeneratorTrait;
}

$app = new BlogApplication();

require __DIR__.'/../app/config/dev.php';
require __DIR__ . '/../app/app.php';
require __DIR__.'/../app/post_routing.php';
require __DIR__.'/../app/comment_routing.php';
require __DIR__.'/../app/subscriber_routing.php';
require __DIR__.'/../app/admin_routing.php';

$app->run();