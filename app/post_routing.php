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

    return $app['twig']->render('post.html.twig', array('post' => $post,
                                                        'blog' => $blogInfo,
                                                  'categories' => $categories,
                                                      'author' => $author));
})->bind('post');

//Create a new post
$app->post('/create_post', function (Request $request) use($app) {
    $author = $app['dao.admin']->get();
    $countRow = $app['dao.post']->createPost($request, $author);
    $msg = $countRow.' row(s) added!\n';

    if($countRow > 0) {
        $response = new Response($msg, 201);
    }
    else {
        $response = new Response($msg, 404);
    }
    return $response;
});

//Edit a post by its id
$app->post('/update', function (Request $request) use($app) {
    $countRow = $app['dao.post']->updatePost($request);

    $msg = $countRow.' row(s) updated!\n';

    if($countRow > 0) {
        $response = new Response($msg, 201);
    }
    else {
        $response = new Response($msg, 404);
    }
    return $response;
});

//Path to publish a post by Id
$app->post('/publish/', function (Request $request) use($app) {
    $count = $app['dao.post']->publishPost($request->get('id'));
    $post = $app['dao.post']->getPost($request->get('id'));
    $data = array('post_status' => "");

    if($count > 0) {
        $data = array('post_status' => $post->getEStatus());
        return $app->json($data, 201);
    }
    else {
        return $app->json($data, 404);
    }

})->bind('published_post');

//Path to hide a post by Id
$app->post('/hide/', function (Request $request) use($app) {

    $count = $app['dao.post']->hidePost($request->get('id'));
    $post = $app['dao.post']->getPost($request->get('id'));
    $data = array('post_status' => "");

    if($count > 0) {
        $data = array('post_status' => $post->getEStatus());
        return $app->json($data, 201);
    }
    else {
        return $app->json($data, 404);
    }
})->bind('hidden_post');

//Delete a post identified by its Id
$app->get('/del/post/{id}', function ($id) use($app) {
   $countDeletion = $app['dao.post']->deletePost($id);

    return $countDeletion;
})->bind('delete_post');
