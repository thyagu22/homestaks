<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Model\Entity\User;
use App\Utils\View;
use App\Session\Admin\Login as SessionAdminLogin;
use WilliamCosta\DatabaseManager\Database;

class Login extends Page{

    /**
     * Retorna a renderização da página login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request,$errorMessage = null)
    {
        //STATUS
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage): '';

        //CONTEÚDO DA PÁGINA LOGIN
        $content = View::render('admin/login', [
            'status' => $status
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Login > TreinaTEK', $content);
    }

    /**
     * Define o login do user
     * @param Request $request
     * @return string|void
     */
    public static function setLogin(Request $request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //BUSCA USER PELO EMAIL
        $obUser = User::getUserByEmail($email);
        if(!$obUser instanceof User){
            return self::getLogin($request, 'Ooops! E-mail ou senha inválidos :(');
        }

        //VERIFICA A SENHA DO USUÁRIO
        if(!password_verify($senha,$obUser->senha)){
            return self::getLogin($request, 'Ooops! E-mail ou senha inválidos :(');
        }

        //CRIA SESSÃO DE LOGIN
        SessionAdminLogin::login($obUser);

        $request->getRouter()->redirect('/admin');
    }

    /**
     * Desloga o usuário
     * @param Request $request
     */
    public static function setLogout($request){
        //DESTROI A SESSÃO DE LOGIN
        SessionAdminLogin::Logout($obUser);

        //REDIRECIONA O USUÁRIO PARA A TELA DE LOGIN
        $request->getRouter()->redirect('/admin/login');
    }
}