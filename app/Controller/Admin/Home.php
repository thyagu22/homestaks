<?php

namespace App\Controller\Admin;


use App\Utils\View;
use App\Http\Request;


class Home extends Page{

    /**
     * Retorna a renderização a view de home do painel
     * @param Request $request
     * @return string
     */
    public static function getHome($request)
    {
        //CONTEÚDO DA HOME
        $content = View::render('admin/modules/home/index',[]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Home > TreinaTEK', $content,'home');
    }


}