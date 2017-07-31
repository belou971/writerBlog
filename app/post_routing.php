<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 16/05/2017
 * Time: 11:12
 */

use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Home page
$app->get('/', function () use ($app) {

    $posts = $app['dao.post']->find(0, 10);
    $blogInfo = $app['dao.blog']->find();
    $categories = $app['dao.category']->findAll();
    $author = $app['dao.admin']->get();

    return $app['twig']->render('index.html.twig', array('posts' => $posts,
                                                         'blog' => $blogInfo,
                                                         'categories' => $categories,
                                                         'author' => $author));
})->bind('home');

//Get Post(Id) page
$app->get('/post/{id}', function ($id) use ($app) {

    $post = $app['dao.post']->getPost($id);
    $blogInfo = $app['dao.blog']->find();
    $categories = $app['dao.category']->findAll();
    $author = $app['dao.admin']->get();
    $comments = $app['dao.comment']->getCommentsByPostId($id);

    return $app['twig']->render('post.html.twig', array('post' => $post,
                                                        'blog' => $blogInfo,
                                                  'categories' => $categories,
                                                      'author' => $author,
                                                'comments'=>$comments));
})->bind('post');

//Create a new post
$app->post('/admin/create_post', function (Request $request) use($app) {
    $data = array('nb_row' => 0, 'post_id' => 0);
    $token = $app['security.token_storage']->getToken();
    if(is_null($token) || is_null($token->getUser())) {
        return $app->redirect($app->path('admin_home'));
    }

    $user = $token->getUser();
    $data = $app['dao.post']->createPost($request, $user->getId());

    if($data['nb_row'] > 0){
        return $app->redirect($app->path('edit_post_form',array('id'=> $data['post_id'])));
    }

    return $app->redirect($app->path('admin_home'));

})->bind('create_post');

//Edit a post by its id
$app->post('/admin/update', function (Request $request) use($app) {
    //$data = array('nb_row' => 0, 'post_id' => 0);
    $token = $app['security.token_storage']->getToken();
    if(is_null($token) || is_null($token->getUser())) {
        return $app->redirect($app->path('admin_home'));
    }


    $app['dao.post']->updatePost($request);

    return $app->redirect($app->path('admin_home'));

})->bind('update_post');

//Path to publish a post by Id
$app->post('/admin/publish/', function (Request $request) use($app) {
    $token = $app['security.token_storage']->getToken();

    $data = array('post_status' => "", 'nbPublishedPost' => 0, 'nbUnpublishedPos' => 0);
    if(is_null($token) || is_null($token->getUser())) {
        return $app->json($data);
    }

    $data['post_status']       = $app['dao.post']->publishPost($request->get('id'));
    $user                      = $token->getUser();
    $data['nbPublishedPost']   = $app['dao.post']->getNumberOfPublishedPost($user->getUsername());
    $data['nbUnpublishedPost'] = $app['dao.post']->getNumberOfUnpublishedPost($user->getUsername());

    return $app->json($data);

})->bind('published_post');

//Path to hide a post by Id
$app->post('/admin/hide/', function (Request $request) use($app) {
    $token = $app['security.token_storage']->getToken();

    $data = array('post_status' => "", 'nbPublishedPost' => 0, 'nbUnpublishedPos' => 0);
    if(is_null($token) || is_null($token->getUser())) {
        return $app->json($data);
    }

    $data['post_status']       = $app['dao.post']->hidePost($request->get('id'));
    $user                      = $token->getUser();
    $data['nbPublishedPost']   = $app['dao.post']->getNumberOfPublishedPost($user->getUsername());
    $data['nbUnpublishedPost'] = $app['dao.post']->getNumberOfUnpublishedPost($user->getUsername());

    return $app->json($data);

})->bind('hidden_post');

//Delete a post identified by its Id
$app->get('/admin/del/post/{id}', function ($id) use($app) {
   $countDeletion = $app['dao.post']->deletePost($id);

    return $countDeletion;
})->bind('delete_post');
