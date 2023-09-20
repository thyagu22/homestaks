<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Tarefas extends Page {

    /**
     * Método responsável por retornar o conteúdo (view) da home
     * @return string
     */
    public static function getTarefas(){
        $obOrganization = new Organization;
        //VIEW DA HOME
        $content = View::render('pages/tarefas',[
            'name' => $obOrganization->name
        ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('TAREFAS > HomeTasks', $content);
    }
}