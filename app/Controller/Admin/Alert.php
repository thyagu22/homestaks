<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Alert
{

    /**
     * Retorna mensagem de sucesso
     * @param string $mensagem
     * @return string
     */
    public static function getSuccess($mensagem){
        return View::render('admin/alert/status',[
            'tipo' => 'success',
            'mensagem' => $mensagem
        ]);

    }

    /**
     * Retorna mensagem de Error
     * @param string $mensagem
     * @return string
     */
    public static function getError($mensagem){
        return View::render('admin/alert/status',[
            'tipo' => 'danger',
            'mensagem' => $mensagem
        ]);

    }

    /**
     * Retorna mensagem de Info
     * @param string $mensagem
     * @return string
     */
    public static function getInfo($mensagem){
        return View::render('admin/alert/status',[
            'tipo' => 'info',
            'mensagem' => $mensagem
        ]);

    }

}