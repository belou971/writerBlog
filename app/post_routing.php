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

    $posts = $app['dao.post']->find(0, 5);
    $blogInfo = $app['dao.blog']->find();
    $categories = $app['dao.category']->findAll();
    $author = $app['dao.admin']->get();
    $commentInfos = $app['dao.comment']->getCommentInfos();
    $lastPostsInfos = $app['dao.post']->findLastPost(4);

    /*end($posts);
    $testIdx = key($posts);
    reset($posts);*/

    return $app['twig']->render('index.html.twig', array('posts' => $posts,
                                                         'lastIdx' => 4,
                                                         'blog' => $blogInfo,
                                                         'categories' => $categories,
                                                         'author' => $author,
                                                         'commentInfos' => $commentInfos,
                                                         'lastPostsInfos' => $lastPostsInfos));
})->bind('home');

//Get Post(Id) page
$app->get('/post/{id}', function ($id) use ($app) {

    $post = $app['dao.post']->getPost($id);
    $blogInfo = $app['dao.blog']->find();
    $categories = $app['dao.category']->findAll();
    $author = $app['dao.admin']->get();
    $comments = $app['dao.comment']->getCommentsByPostId($id);
    $commentInfos = $app['dao.comment']->getCommentInfos();
    $lastPostsInfos = $app['dao.post']->findLastPost(4);


    return $app['twig']->render('post.html.twig', array('post' => $post,
                                                        'blog' => $blogInfo,
                                                  'categories' => $categories,
                                                      'author' => $author,
                                                     'comments'=>$comments,
                                                'commentInfos' => $commentInfos,
                                              'lastPostsInfos' => $lastPostsInfos));
})->bind('post');

//Find the next post series given by the click on the button 'more'
$app->get('/more/{idx}', function ($idx) use ($app) {
    $posts = $app['dao.post']->find($idx, 5);
    $categories = $app['dao.category']->findAll();
    $commentInfos = $app['dao.comment']->getCommentInfos();
    $author = $app['dao.admin']->get();

    $isLast = count($posts) < 5;
    $lastIdx = $idx + 5 - 1;

    $html = $app['twig']->render('sections/post_list.html.twig', array('posts' => $posts,
                                                                       'categories' => $categories,
                                                                       'commentInfos' => $commentInfos,
                                                                       'author' => $author));
    $data = array('content' => $html, 'idx' => $lastIdx, 'hide' => $isLast);

    return $app->json($data);
});


