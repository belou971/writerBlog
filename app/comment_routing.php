<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 16/05/2017
 * Time: 11:12
 */

use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


//Services associated to Comment data
$app->get('/comment/{id}', function ($id) use($app) {

 $commentsTree = $app['dao.comment']->getCommentsByPostId($id);

    var_dump($commentsTree);
    //return $commentsTree;
    return "ok";
});

//Add a new comment on a post
$app->post('/comment/add', function (Request $resquestForm) use($app) {

    $count = $app['dao.comment']->createComment($resquestForm);

    $msg = $count.' row(s) added!\n';

    if($count > 0) {
        $response = new Response($msg, 201);
    }
    else {
        $response = new Response($msg, 404);
    }
    return $response;

});

//Reply to a comment on a post
$app->get('/comment_{id}/reply', function ($id) use($app) {

    $commentToReply = $app['dao.comment']->getComment($id);

    $message="Comment found!!";
    if(is_null($commentToReply)) {$message="Comment not found!!";}

    var_dump($commentToReply);

    //TODO:set the comment form to reply from commentToReply as parent id and post id

    return $message;
});

//Make an alert on a comment
$app->post('/comment/alert', function (Request $resquestForm) use($app) {

    $count = $app['dao.comment']->alertComment($resquestForm);

    $msg = $count.' row(s) updated!\n';

    if($count > 0) {
        $response = new Response($msg, 201);
    }
    else {
        $response = new Response($msg, 404);
    }

    //TODO:set the comment form to  from comment to alert as parent id and post id

    return $response;
});
