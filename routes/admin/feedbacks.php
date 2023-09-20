<?php


use \App\Http\Response;
use \App\Controller\Admin;
use App\Http\Request;

//ROTA LISTAGEM DE FEEDBACKS
$obRouter->get('/admin/feedbacks', [
    'middlewares' => [
        'required-admin-login',
        'cache'
    ],
    function($request) {
        return new Response(200, Admin\Feedback::getFeedbacks($request));
    }
]);

//ROTA CADASTRO DE FEEDBACKS
$obRouter->get('/admin/feedbacks/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Feedback::getNewFeedback($request));
    }
]);

//ROTA CADASTRO DE FEEDBACKS  (POST)
$obRouter->post('/admin/feedbacks/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Feedback::setNewFeedback($request));
    }
]);

//ROTA EDIÇÃO DE FEEDBACKS
$obRouter->get('/admin/feedbacks/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id ) {
        return new Response(200, Admin\Feedback::getEditFeedback($request,$id));
    }
]);

//ROTA EDIÇÃO DE FEEDBACKS(POST)
$obRouter->post('/admin/feedbacks/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id ) {
        return new Response(200, Admin\Feedback::setEditFeedback($request,$id));
    }
]);

//ROTA EXCLUSÃO DE FEEDBACKS
$obRouter->get('/admin/feedbacks/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id) {
        return new Response(200, Admin\Feedback::getDeleteFeedback($request,$id));
    }
]);

//ROTA EXCLUSÃO DE FEEDBACKS(POST)
$obRouter->post('/admin/feedbacks/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id ) {
        return new Response(200, Admin\Feedback::setDeleteFeedback($request,$id));
    }
]);

