<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Model\Entity\User;
use Closure;


class JWTAuth
{

    /**
     * Retorna uma instancia de USER autenticado
     * @return User
     * @param Request $request
     *
     */
    private static function getJWTAuthUser($request)
    {
        //HEADERS
        $headers = $request->getHeaders();
        echo "<pre>";
        print_r($headers);
        echo "<pre>";
        exit;



        //BUSCA USER POR EMAIL
        $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);

        //VERIFICA A INSTANCIA
        if(!$obUser instanceof User){
            return false;
        }

        //VALIDA A SENHA E RETORNA O USER
        return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;

    }


    /**
     * Valida o acesso via JWT
     * @param Request $request
     */
    private function auth($request){

        //VERIFICA O USER RECEBIDO
        if($obUser = $this->getJWTAuth($request)){
            $request->user = $obUser;
            return true;
        }

        //EMITE ERRO DE SENHA INVALIDA
        throw new \Exception("Token inválido", 403);

    }

    /**
     * Executa a middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        //REALIZA A VALIDAÇÃO DO ACESSO VIA JWT
        $this->auth($request);

        //EXECUTA O PROXIMO NÍVEL DO MIDDLEWARE
       return $next($request);
    }

}