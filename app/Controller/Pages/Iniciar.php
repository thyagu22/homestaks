<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Iniciar extends Page {

    /**
     * Método responsável por retornar o conteúdo (view) da sobre
     * @return string
     */
    public static function getIniciar(){
        $obOrganization = new Organization;
        //VIEW DA HOME
        $content = View::render('pages/iniciar',[
            'name' => $obOrganization->name,
            'site' => $obOrganization->site,
            'description' => $obOrganization->description
        ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('INICIAR TAREFA > Home Tasks', $content);
    }
}