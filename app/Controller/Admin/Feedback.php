<?php

namespace App\Controller\Admin;


use App\Model\Entity\Feedback as EntityFeedback;
use App\Utils\View;
use App\Http\Request;
use WilliamCosta\DatabaseManager\Pagination;


class Feedback extends Page
{

    /**
     * obtem a renderização dos itens de feedback para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getFeedbackItems($request, &$obPagination)
    {
        //FEEDBACKS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityFeedback::getFeedbacks(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTÂCIA DE PAGINAÇAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = EntityFeedback::getFeedbacks(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obFeedback = $results->fetchObject(EntityFeedback::class)) {
            $itens .= View::render('admin/modules/feedbacks/item', [
                'id' => $obFeedback->id,
                'nome' => $obFeedback->nome,
                'mensagem' => $obFeedback->mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obFeedback->data))
            ]);

        }
        //RETORNA OS FEEDBACKS
        return $itens;

    }

    /**
     * Renderiza a view de listagem de feedbacks
     * @param Request $request
     * @return string
     */
    public static function getFeedbacks($request)
    {
        //CONTEÚDO DA HOME
        $content = View::render('admin/modules/feedbacks/index', [
            'itens' => self::getFeedbackItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);


        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Feedbacks > TreinaTEK', $content, 'feedbacks');
    }

    /**
     * Retorna formulário de cadastro de novo feedback
     * @param Request $request
     * @return string
     */
    public static function getNewFeedback($request)
    {
        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/feedbacks/form', [
            'title' => 'Cadastrar Feedbacks',
            'nome' => '',
            'mensagem' => '',
            'status' => ''


        ]);

        //CADASTRAR FEEDBACK
        return parent::getPanel('Cadastrar Feedback > TreinaTEK', $content, 'feedbacks');
    }

    /**
     * Cadastra no DB o novo feedback
     * @param Request $request
     */
    public static function setNewFeedback($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //NOVA INSTANCIA DE FEEDBACKS
        $obFeedback = new EntityFeedback();
        $obFeedback->nome = $postVars['nome'] ?? '';
        $obFeedback->mensagem = $postVars['mensagem'] ?? '';
        $obFeedback->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/feedbacks/' . $obFeedback->id . '/edit?status=created');

    }

    /**
     * Retorna mesagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if(!isset($queryParams['status'])) return '';

        //MENSAGENS DE STATUS
        switch($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Feedback criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Feedback atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Feedback excluido com sucesso!');
                break;
        }

    }

    /**
     * Retorna formulário de edição de feedback
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditFeedback($request, $id)
    {
        //OBTEM FEEDBACK DO DB
        $obFeedback = EntityFeedback::getFeedbackById($id);

        //VALIDA A INSTANCIA
        if(!$obFeedback instanceof EntityFeedback) {
            $request->getRouter()->redirect('admin/feedbacks');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/feedbacks/form', [
            'title' => 'Editar Feedbacks',
            'nome' => $obFeedback->nome,
            'mensagem' => $obFeedback->mensagem,
            'status' => self::getStatus($request)


        ]);

        //CADASTRAR FEEDBACK
        return parent::getPanel('Editar Feedback > TreinaTEK', $content, 'feedbacks');
    }

    /**
     * Grava a edição de feedback
     * @param Request $request
     * @param integer $id
     */
    public static function setEditFeedback($request, $id)
    {
        //OBTEM FEEDBACK DO DB
        $obFeedback = EntityFeedback::getFeedbackById($id);

        //VALIDA A INSTANCIA
        if(!$obFeedback instanceof EntityFeedback) {
            $request->getRouter()->redirect('admin/feedbacks');
        }

        //POST VARS
        $posVars = $request->getPostVars();

        //ATUALIZA A INSTANCIA
        $obFeedback->nome = $posVars['nome'] ?? $obFeedback->nome;
        $obFeedback->mensagem = $posVars['mensagem'] ?? $obFeedback->mensagem;
        $obFeedback->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/feedbacks/' . $obFeedback->id . '/edit?status=updated');

    }

    /**
     * Retorna formulário de exclusão de feedback
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteFeedback($request, $id)
    {
        //OBTEM FEEDBACK DO DB
        $obFeedback = EntityFeedback::getFeedbackById($id);

        //VALIDA A INSTANCIA
        if(!$obFeedback instanceof EntityFeedback) {
            $request->getRouter()->redirect('/admin/feedbacks');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('/admin/modules/feedbacks/delete', [

            'nome' => $obFeedback->nome,
            'mensagem' => $obFeedback->mensagem

        ]);

        //CADASTRAR FEEDBACK
        return parent::getPanel('Excluir Feedback > TreinaTEK', $content, 'feedbacks');
    }

    /**
     * Exclui feedback do DB
     * @param Request $request
     * @param integer $id
     */
    public static function setDeleteFeedback($request, $id)
    {
        //OBTEM FEEDBACK DO DB
        $obFeedback = EntityFeedback::getFeedbackById($id);

        //VALIDA A INSTANCIA
        if(!$obFeedback instanceof EntityFeedback) {
            $request->getRouter()->redirect('admin/feedbacks');
        }

        //POST VARS
        $posVars = $request->getPostVars();

        //ATUALIZA A INSTANCIA
        $obFeedback->nome = $posVars['nome'] ?? $obFeedback->nome;
        $obFeedback->mensagem = $posVars['mensagem'] ?? $obFeedback->mensagem;
        $obFeedback->deletar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/feedbacks?status=deleted');

    }


}