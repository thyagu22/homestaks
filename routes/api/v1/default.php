<?php

use App\Http\Response;
use App\Controller\Api\Api;

//ROTA RAIZ API
$obRouter->get('/api/v1', [
    function($request){
    return new Response(200, Api::getDetails($request), 'application/json');
    }
]);
