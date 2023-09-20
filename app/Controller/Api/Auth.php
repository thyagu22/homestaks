<?php

namespace App\Controller\Api;

use App\Controller\Admin\Users;
use App\Http\Request;
use App\Model\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends Api
{

    /**
     * Gera um Token JWT
     * @param Request $request
     * @return array
     */
    public static function generateToken($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['email'])  or !isset($postVars['senha'])){
            throw new \Exception("Os campos 'email' e 'senha' são obrigatórios", 400);

        }

        //BUSCA USER PELO EMAIL
        $obUser = User::getUserByEmail($postVars['email']);
        if(!$obUser instanceof User){
            throw new \Exception("Usuário ou senha inválidos",400);
        }

        //VALIDA A SENHA DO USER
        if(!password_verify($postVars['senha'],$obUser->senha)){
            throw new \Exception("Usuário ou senha inválidos",400);
        }

        //PAYLOAD
        $payload = [
            'email' => $obUser->email
        ];

        //RETORNA O TOKEN GERADO
        return  [
            'token' => JWT::encode($payload,getenv('JWT_KEY'),'HS256')
        ];


    }
}