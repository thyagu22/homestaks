<?php

use App\Http\Response;
use App\Controller\Api\Api;
use App\Controller\Api\User;

//ROTA RAIZ API
$obRouter->get('/api/v1/users', [
    'middlewares' => [
        'api',
        'cache'
    ],
    function($request){
    return new Response(200, User::getUsers($request), 'application/json');
    }
]);

//ROTA CONSULTA INDIVIDUAL DE USERS
$obRouter->get('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'cache'
    ],
    function($request,$id){
        return new Response(200, User::getUser($request,$id), 'application/json');
    }
]);

//ROTA CADASTRO DE USER
$obRouter->post('/api/v1/users', [
    'middlewares' => [
        'api'

    ],
    function($request){
        return new Response(200, User::setNewUser($request), 'application/json');
    }
]);

//ROTA EDIÇÃO DE USER
$obRouter->put('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(200, Feedback::setEditFeedback($request, $id), 'application/json');
    }
]);

//ROTA EXCLUXÃO DE USER
$obRouter->delete('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(200, User::setDeleteUser($request, $id), 'application/json');
    }
]);
