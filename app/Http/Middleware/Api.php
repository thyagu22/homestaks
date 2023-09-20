<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Closure;

class Api
{
    /**
     * Executa a middleware
     * @param Request$request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        //ALTERA O CONTENTTYPE PARA JSON
        $request->getRouter()->setContentType('application/json');


        //EXECUTA O PROXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }

}