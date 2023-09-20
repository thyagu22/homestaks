<?php

use App\Http\Response;
use App\Controller\Api\Api;
use App\Controller\Api\Feedback;

//ROTA RAIZ API
$obRouter->get('/api/v1/charts', [
    'middlewares' => [
        'api',
        'cache'
    ],
    function($request){
    return new Response(200, \App\Controller\Api\Chart::getCharts($request), 'application/json');
    }
]);

//ROTA CONSULTA INDIVIDUAL DE FEEDBACKS
$obRouter->get('/api/v1/charts/{id}', [
    'middlewares' => [
        'api',
        'cache'
    ],
    function($request,$id){
        return new Response(200, \App\Controller\Api\Chart::getChart($request,$id), 'application/json');
    }
]);

//ROTA CADASTRO DE FEEDBACKS
$obRouter->post('/api/v1/feedbacks', [
    'middlewares' => [
        'api',
        'user-basic-auth'

    ],
    function($request){
        return new Response(200, Feedback::setNewFeedback($request), 'application/json');
    }
]);

//ROTA EDIÇÃO DE FEEDBACKS
$obRouter->put('/api/v1/feedbacks/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(200, Feedback::setEditFeedback($request, $id), 'application/json');
    }
]);

//ROTA EXCLUXÃO DE FEEDBACKS
$obRouter->delete('/api/v1/feedbacks/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(200, Feedback::setDeleteFeedback($request, $id), 'application/json');
    }
]);
