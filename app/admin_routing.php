<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 09/06/2017
 * Time: 14:12
 */

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

// Connexion form page
$app->get('/connexion', function(Request $request) use ($app) {

    $blogInfo = $app['dao.blog']->find();

    return $app['twig']->render('connexion.html.twig', array('blog'  => $blogInfo,
                                                             'error' => $app['security.last_error']($request),
                                                             'last_username' => $app['session']->get('_security.last_username'),
    ));

})->bind('connexion');


function buildAdminHome($app) {
    $blogInfo        = $app['dao.blog']->find();
    $token           = $app['security.token_storage']->getToken();

    if(is_null($token)) {
        return NULL;
    }

    $user              = $token->getUser();
    $nbNewReports      = $app['dao.comment']->getNumberOfReportedComments();
    $nbNewComments     = $app['dao.comment']->getNumberOfNewComments($user->getWebName());
    $nbPublishedPost   = $app['dao.post']->getNumberOfPublishedPost($user->getUsername());
    $nbUnpublishedPost = $app['dao.post']->getNumberOfUnpublishedPost($user->getUsername());
    $availablePosts    = $app['dao.post']->getAvailablePostsCreatedBy($user->getUsername());
    $categories        = $app['dao.category']->findAll();
    $admin             = $app['dao.admin']->get();

    return $app['twig']->render('post-admin.html.twig', array('blog'  => $blogInfo,
        'nbNewReports' => $nbNewReports,
        'nbNewComments' => $nbNewComments,
        'nbPublishedPost' => $nbPublishedPost,
        'nbUnpublishedPost' => $nbUnpublishedPost,
        'posts' => $availablePosts,
        'categories' => $categories,
        'admin' => $admin));
}

// Administration home page
$app->get('/admin/', function() use ($app) {
    return buildAdminHome($app);
})->bind('admin_home');

$app->get('/admin/cancel', function() use ($app) {
    return $app->redirect($app->path('admin_home'));
});

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


// Adminstration: New post form page
$app->get('/admin/new-post', function() use ($app) {
    $blogInfo = $app['dao.blog']->find();
    $categories = $app['dao.category']->findAll();

    return $app['twig']->render('form-new-post.html.twig', array('blog'  => $blogInfo,
                                                                 'categories' => $categories,
                                                                       'post' => null));

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
    $token           = $app['security.token_storage']->getToken();

    if(is_null($token)) {
        return NULL;
    }

    $user              = $token->getUser();
    $availablePosts    = $app['dao.post']->getAvailablePostsCreatedBy($user->getUsername());
    $categories        = $app['dao.category']->findAll();
    $admin             = $app['dao.admin']->get();

    return $app['twig']->render('post-overview.html.twig', array('blog'  => $blogInfo,
                                                                 'posts' => $availablePosts,
                                                                 'categories' => $categories,
                                                                 'admin' => $admin));

})->bind('posts_overview');

//Administration: get identical category if it exists
$app->post('/admin/createCategory', function(Request $request) use ($app) {
   $categoryData = $app['dao.category']->createCategory($request->get('newCategory'));

    if($categoryData['status'] == true) {
        $categories  = $app['dao.category']->findAll();

        $html = $app['twig']->render('sections/select-categories.html.twig', array('categories' => $categories,
                                                                                         'post' => null));
        $categoryData['message'] = $html;
    }

    $response = $app->json($categoryData);
    return $response;
})->bind('createCategory');

//Administration: new comments page in admin side
$app->get('/admin/comments-overview', function() use ($app) {

    $blogInfo = $app['dao.blog']->find();
    $token    = $app['security.token_storage']->getToken();

    if(is_null($token)) {
        return NULL;
    }

    $user = $token->getUser();
    $newCommentList = $app['dao.comment']->findNewComments($user->getWebName());
    $oldCommentList = $app['dao.comment']->findOldReadComments($user->getWebName());
    $adminCommentsList = $app['dao.comment']->findCommentsByUsername($user->getWebName());

    return $app['twig']->render('comment-view.html.twig', array('blog'  => $blogInfo,
                                                                'newComments' => $newCommentList,
                                                                'oldComments' => $oldCommentList,
                                                              'adminComments' => $adminCommentsList));

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

    $token    = $app['security.token_storage']->getToken();
    if(is_null($token)) {
        return NULL;
    }
    $user = $token->getUser();
    $oldCommentList = $app['dao.comment']->findOldReadComments($user->getWebName());

    $html = $app['twig']->render('sections/already_read_comments_section.html.twig', array('oldComments' => $oldCommentList));
    $data['html'] = $html;

    return $app->json($data);
})->bind('admin_mark_comment');

//Administration: Mark a comment as unread
$app->post('/admin/comment/unread', function(Request $requestForm) use($app) {
    $data = $app['dao.comment']->markAsUnread($requestForm->get('id'));

    $token    = $app['security.token_storage']->getToken();
    if(is_null($token)) {
        return NULL;
    }
    $user = $token->getUser();
    $newCommentList = $app['dao.comment']->findNewComments($user->getWebName());

    $html = $app['twig']->render('sections/unread_comments_section.html.twig', array('newComments' => $newCommentList));
    $data['html'] = $html;

    return $app->json($data);
})->bind('admin_unmark_comment');

//Administration: Approuve a reported comment
$app->post('/admin/comment/publish', function(Request $requestForm) use($app) {
    $data = $app['dao.comment']->publishComment($requestForm->get('id'));

    return $app->json($data);
})->bind('admin_publish_comment');
