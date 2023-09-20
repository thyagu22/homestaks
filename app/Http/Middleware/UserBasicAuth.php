<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Model\Entity\User;
use Closure;


class UserBasicAuth
{

    /**
     * Retorna uma instancia de USER autenticado
     * @return User
     *
     */
    private static function getBasicAuthUser()
    {
        //VERIFICA A EXISTENCIA DOS DADOS DE ACESSO
        if(!isset($_SERVER['PHP_AUTH_USER']) or !isset($_SERVER['PHP_AUTH_PW'])){
            return false;
        }

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
     * Valida o acesso via HTTP BASIC AUTH
     * @param Request $request
     */
    private function basicAuth($request){

        //VERIFICA O USER RECEBIDO
        if($obUser = $this->getBasicAuthUser()){
            $request->user = $obUser;
            return true;
        }

        //EMITE ERRO DE SENHA INVALIDA
        throw new \Exception("Usuário ou senha inválidos", 403);

    }

    /**
     * Executa a middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        //REALIZA A VALIDAÇÃO DO ACESSO VIA BASIC AUTH
        $this->basicAuth($request);

        //EXECUTA O PROXIMO NÍVEL DO MIDDLEWARE
       return $next($request);
    }

}