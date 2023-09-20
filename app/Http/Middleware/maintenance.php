<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;

class maintenance
{
    /**
     * Executa a middleware
     * @param Request$request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        if (getenv('MAINTENANCE') == 'true') {
            throw new \Exception("Página em manutenção, tente novamente mais tarde :-( ",200);
        }

        //EXECUTA O PROXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }

}