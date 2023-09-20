<?php

use \App\Http\Response;
use \App\Controller\Pages;

//ROTA HOME
$obRouter->get('/', [
    'middlewares' => [
        'cache'
    ],
    function () {
        return new Response(200, Pages\Home::getHome());
    }
]);

//ROTA SOBRE
$obRouter->get('/iniciar', [
    'middlewares' => [
        'cache'
    ],
    function () {
        return new Response(200, Pages\Iniciar::getIniciar());
    }
]);

//ROTA DEPOIMENTOS (insert)
$obRouter->post('/iniciar', [
    function ($request) {

        return new Response(200, Pages\Realizadas::insertTarefa($request));
    }
]);


//ROTA DEPOIMENTOS
$obRouter->get('/realizadas', [
    'middlewares' => [
        'cache'
    ],
    function ($request) {
        return new Response(200, Pages\Realizadas::getTarefa($request,0));
    }
]);

$obRouter->post('/realizadas', [
    'middlewares' => [
        'cache'
    ],
    function ($request) {
        return new Response(200, Pages\Realizadas::setDeleteTarefa($request));
    }
]);

$obRouter->post('/realizadas/{id}/edit', [
    'middlewares' => [
        'cache'
    ],
    function ($request) {
        return new Response(200, Pages\Realizadas::setDeleteTarefa($request));
    }
]);

$obRouter->get('/realizadas/{id}/edit', [
    function($request, $id ) {
        return new Response(200, Pages\Realizadas::getTarefa($request,$id));
    }
]);



//ROTA REGISTRO
$obRouter->get('/tarefas', [
    function ($request) {

        return new Response(200, Pages\Tarefas::getTarefas($request));
    }
]);

//ROTA REGISTRO
$obRouter->get('/fechamento', [
    function ($request) {

        return new Response(200, Pages\Fechamento::getFechamento($request,0));
    }
]);



//ROTA REGISTRO
$obRouter->get('/fechamento/{id}/edit', [
    function($request, $id ) {
        return new Response(200, Pages\Fechamento::getFechamento($request,$id));
    }
]);

//ROTA REGISTRO
$obRouter->post('/fechamento/{id}/edit', [
    function($request, $id ) {
        return new Response(200, Pages\Fechamento::insertFechamento($request));
    }
]);

//ROTA REGISTRO
$obRouter->get('/fechamentos', [
    function ($request) {

        return new Response(200, Pages\Fechamento::getFechamentos($request,0));
    }
]);

//ROTA REGISTRO
$obRouter->post('/fechamentos', [
    function ($request) {

        return new Response(200, Pages\Fechamento::setDeleteFechamento($request));
    }
]);


$obRouter->get('/fechamentos/{id}/edit', [
    function($request, $id ) {
        return new Response(200, Pages\Fechamento::getFechamentos($request,$id));
    }
]);

$obRouter->post('/fechamentos/{id}/edit', [
    function($request, $id ) {
        return new Response(200, Pages\Fechamento::setDeleteFechamento($request));
    }
]);



//ROTA DINAMICA
$obRouter->get('/pagina/{idPagina}/{acao}', [
    'middlewares' => [
        'cache'
    ],
    function ($idPagina, $acao) {
        return new Response(200, 'Página' . $idPagina . ' - ' . $acao);
    }
]);