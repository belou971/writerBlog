<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 09/06/2017
 * Time: 14:12
 */

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app->post('/signup-check', function(Request $request) use ($app) {
    $blogInfo = $app['dao.blog']->find();

    $encoder = $app['security.default_encoder'];

    $countRow = $app['dao.admin']->updateAdmin($request, $encoder);

    $msg = $countRow . ' row(s) updated!\n';

    if ($countRow > 0) {
        $response = new Response($msg, 201);
    } else {
        $response = new Response($msg, 404);
    }
    return $response;
})->bind('signup-check');

$app->get('/signup', function(Request $request) use ($app) {

    $blogInfo = $app['dao.blog']->find();
    return $app['twig']->render('sign-up.html.twig', array('blog' => $blogInfo)
    );
})->bind('signup');

// Connexion form page
$app->get('/connexion', function(Request $request) use ($app) {

    $blogInfo = $app['dao.blog']->find();

    return $app['twig']->render('connexion.html.twig', array('blog'  => $blogInfo,
                                                             'error' => $app['security.last_error']($request),
                                                             'last_username' => $app['session']->get('_security.last_username'),
    ));

})->bind('connexion');


// Administration home page
$app->get('/admin/', function() use ($app) {
    $blogInfo        = $app['dao.blog']->find();
    $nbNewComments   = $app['dao.comment']->getNumberOfNewComments();
    $token           = $app['security.token_storage']->getToken();

    if(is_null($token)) {
        return NULL;
    }

    $user              = $token->getUser();

    $nbPublishedPost   = $app['dao.post']->getNumberOfPublishedPost($user->getUsername());
    $nbUnpublishedPost = $app['dao.post']->getNumberOfUnpublishedPost($user->getUsername());
    $availablePosts    = $app['dao.post']->getAvailablePostsCreatedBy($user->getUsername());
    $categories        = $app['dao.category']->findAll();
    $admin             = $app['dao.admin']->get();

    return $app['twig']->render('post-admin.html.twig', array('blog'  => $blogInfo,
                                                              'nbNewComments' => $nbNewComments,
                                                              'nbPublishedPost' => $nbPublishedPost,
                                                              'nbUnpublishedPost' => $nbUnpublishedPost,
                                                              'posts' => $availablePosts,
                                                              'categories' => $categories,
                                                              'admin' => $admin));

})->bind('admin_home');


// Adminstration: New post form page
$app->get('/admin/new-post', function() use ($app) {
    $blogInfo = $app['dao.blog']->find();
    $categories = $app['dao.category']->findAll();

    return $app['twig']->render('form-new-post.html.twig', array('blog'  => $blogInfo,
                                                                 'categories' => $categories));

})->bind('new_post_form');


// Administration: Post Edition Form page
$app->get('/admin/edit-post/{id}', function($id) use ($app) {
    $blogInfo          = $app['dao.blog']->find();
    $categories        = $app['dao.category']->findAll();
    $admin             = $app['dao.admin']->get();
    $post              = $app['dao.post']->getPost($id);

    return $app['twig']->render('form-edit-post.html.twig', array('blog'  => $blogInfo,
        'categories' => $categories,
        'admin' => $admin,
        'post' => $post));

})->bind('edit_post_form');


// Administration: List of editable posts
$app->get('/admin/posts-overview', function() use ($app) {
    $blogInfo = $app['dao.blog']->find();

    return $app['twig']->render('post-overview.html.twig', array('blog'  => $blogInfo));

})->bind('posts_overview');

//Administration: get identical category if it exists
$app->post('/admin/existCategory', function(Request $request) use ($app) {
   $categoryData = $app['dao.category']->existCategory($request->get('newCategory'));

    if(count($categoryData) > 0) {
        $response = $app->json($categoryData);
    }
    else {
        $categoryData = array('cat_name' => "",
                              'cat_counter' => "0",
                              'message' => "Une erreur s'est produit à la lecture du service");
        $response = $app->json($categoryData, 304);
    }

    return $response;
})->bind('existCategory');

//Administration: new comments page in admin side
$app->get('/admin/comments-overview', function() use ($app) {

    $blogInfo = $app['dao.blog']->find();
    $token    = $app['security.token_storage']->getToken();

    if(is_null($token)) {
        return NULL;
    }

    $user = $token->getUser();
    $newCommentList = $app['dao.comment']->findNewComments($user->getWebName());

    return $app['twig']->render('comment-view.html.twig', array('blog'  => $blogInfo,
                                                                'newComments' => $newCommentList));

})->bind('comments-overview');

//Administration:
$app->get('/admin/comment/report-view', function () use($app) {

    $blogInfo = $app['dao.blog']->find();
    $reportCommentList = $app['dao.comment']->findReportedComments();

    return $app['twig']->render('report-view.html.twig', array('blog'  => $blogInfo,
                                                               'reportedComments' => $reportCommentList));
})->bind('report_view');

//Administration: admin can reply to a comment
//Add a new comment on a post
$app->post('/admin/comment/add', function (Request $requestForm) use($app) {
    $token = $app['security.token_storage']->getToken();

    if(is_null($token)) {
        return NULL;
    }

    $user = $token->getUser();
    var_dump($user);

    $requestForm->attributes->set('email', $user->getEmail());
    $requestForm->attributes->set('name', $user->getWebName());
    $data = $app['dao.comment']->createComment($requestForm);

    if($data['parent_id'] > 0) {
        $app['dao.comment']->markAsRead($data['parent_id']);
    }

    $blogInfo = $app['dao.blog']->find();
    $newCommentList = $app['dao.comment']->findNewComments($user->getWebName());

    return $app->redirect($app->path('comments-overview', array('blog' => $blogInfo, 'newComments' => $newCommentList)));
    //return $app->json($data);

})->bind('admin_add_comment');

//Administration: delete an comment by the admin
$app->post('/admin/comment/delete', function (Request $requestForm) use($app) {
    $data = $app['dao.comment']->deleteComment($requestForm->get('id'));

    return $app->json($data);

})->bind('admin_delete_comment');

//Administration: Mark a comment as read
$app->post('/admin/comment/read', function(Request $requestForm) use($app) {
    $data = $app['dao.comment']->markAsRead($requestForm->get('id'));

    return $app->json($data);
})->bind('admin_mark_comment');

//Administration: Approuve a reported comment
$app->post('/admin/comment/publish', function(Request $requestForm) use($app) {
    $data = $app['dao.comment']->publishComment($requestForm->get('id'));

    return $app->json($data);
})->bind('admin_publish_comment');
