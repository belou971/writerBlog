<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 08/06/2017
 * Time: 18:36
 */

use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


//Services associated to Subscriber data
$app->post('/subscribe', function (Request $request) use($app) {

    $count = $app['dao.subscriber']->addSubscriber($request);

    $msg = $count.' row(s) added!\n';

    if($count > 0) {
        $response = new Response($msg, 201);
    }
    else {
        $response = new Response($msg, 404);
    }
    return $response;
});