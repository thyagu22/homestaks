<?php

namespace App\Session\Admin;

use App\Model\Entity\User;

class Login
{

    /**
     * Inicia a sessãa
     */
    private static function init()
    {
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    /**
     * Cria login do usuário
     * @param User $obUser
     * @return boolean
     */
    public static function login($obUser)
    {
        //INICIA A SESSÃO
        self::init();

        //DEFINE A SESSÃO DO USUÁRIO
        $_SESSION['admin']['usuario'] = [
            'id' => $obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];

        //SUCESSO
        return true;

    }

    /**
     * Verifica se o usuário está logado
     * @return boolean
     */
    public static function isLogged()
    {
        //INICIA A SESSÃO
        self::init();

        //RETORNA A VERIFICAÇÃO
        return isset($_SESSION['admin']['usuario']['id']);

    }

    /**
     * Executa logout
     * @return boolean
     */
    public static function logout()
    {
        //INICIA A SESSÃO
        self::init();

        //DESLOGA
        unset($_SESSION['admin']['usuario']);

        //SUCESSO
        return true;

    }



}