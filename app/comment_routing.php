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

    return "ok";
});

//Open comment form on a post
$app->post('/open_comment_form/', function(Request $request) use($app) {
    $post_id = $request->get('post_id');
    $parent_id = $request->get('parent_id');

    $html = $app['dao.comment']->getForm($post_id, $parent_id);

    return new Response($html);
});

//Add a new comment on a post
$app->post('/comment/add', function (Request $requestForm) use($app) {

    $data = $app['dao.comment']->createComment($requestForm);

    return $app->redirect($app->path('post', array('id' => $data['post_id'])));

})->bind('add_comment');

//Reply to a comment on a post
$app->get('/comment_{id}/reply', function ($id) use($app) {

    $commentToReply = $app['dao.comment']->getComment($id);

    $message="Comment found!!";
    if(is_null($commentToReply)) {$message="Comment not found!!";}

    return $message;
});

//Sent an alert about a comment
$app->post('/comment/alert', function (Request $requestForm) use($app) {

    $count = $app['dao.comment']->alertComment($requestForm);

    $msg = $count.' row(s) updated!\n';

    $response = new Response($msg);

    return $response;
});
