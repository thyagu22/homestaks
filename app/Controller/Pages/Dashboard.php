<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Dashboard extends Page {

    /**
     * Método responsável por retornar o conteúdo (view) da home
     * @return string
     */
    public static function getDashboard(){
        $obOrganization = new Organization;
        //VIEW DA HOME
        $content = View::render('pages/dashboard',[
            'name' => $obOrganization->name
        ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('DASHBOARD  TreinaTEK', $content);
    }
}