<?php


use \App\Http\Response;
use \App\Controller\Admin;
use App\Http\Request;

//ROTA LISTAGEM DE FEEDBACKS
$obRouter->get('/admin/users', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Users::getUsers($request));
    }
]);

//ROTA CADASTRO DE FEEDBACKS
$obRouter->get('/admin/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Users::getNewUser($request));
    }
]);

//ROTA CADASTRO DE FEEDBACKS  (POST)
$obRouter->post('/admin/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\Users::setNewUser($request));
    }
]);

//ROTA EDIÇÃO DE FEEDBACKS
$obRouter->get('/admin/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id ) {
        return new Response(200, Admin\Users::getEditUser($request,$id));
    }
]);

//ROTA EDIÇÃO DE FEEDBACKS(POST)
$obRouter->post('/admin/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id ) {
        return new Response(200, Admin\Users::setEditUser($request,$id));
    }
]);

//ROTA EXCLUSÃO DE FEEDBACKS
$obRouter->get('/admin/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id) {
        return new Response(200, Admin\Users::getDeleteUser($request,$id));
    }
]);

//ROTA EXCLUSÃO DE FEEDBACKS(POST)
$obRouter->post('/admin/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id ) {
        return new Response(200, Admin\Users::setDeleteUser($request,$id));
    }
]);

