<?php

namespace App\Controller\Pages;

use App\Model\Entity\Fechamento as EntityFechamento;
use App\Model\Entity\Feedback as EntityFeedback;
use \App\Utils\View;
use App\Http\Request;
use \App\Model\Entity\Tarefas as EntityTarefas;
use WilliamCosta\DatabaseManager\Database;
use WilliamCosta\DatabaseManager\Pagination;


class Realizadas extends Page
{

    /**
     * obtem a renderização dos itens de feedback para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTarefasItems($request,$id)
    {
        //FEEDBACKS
        $itens = '';
        $where = '';

        $mes = date("m");
        $ano = '20'.date("y");

        switch($id){
            case 0 :
                $where = 'Month(dt_tarefa) = '.$mes.' and year(dt_tarefa) = '.$ano;
                break;
            case 2:
            case 1 :
                $where = 'cdfilho = '.$id.' and Month(dt_tarefa) = '.$mes.' and year(dt_tarefa) = '.$ano;
                break;

        }

        //$obTarefa
        $results  = EntityTarefas::getTarefas($where,null,'id desc',null,'id, cdfilho, cdtarefa, dt_tarefa');

        $filhoAtivo = '';
        $tarefa = '';

        while($obTarefa = $results->fetchObject(EntityFechamento::class)){
            switch($obTarefa->cdfilho) {

                case 1 :
                    $filhoAtivo = 'Alice';
                    break;

                case 2 :
                    $filhoAtivo = 'Arthur';
                    break;
            }

            switch($obTarefa->cdtarefa) {

                case 'gr' :
                    $tarefa = 'Guardar Roupas';
                    break;

                case 'fs' :
                    $tarefa = 'Faxina de Sexta';
                    break;

                case 'lc' :
                    $tarefa = 'Limpar Chão';
                    break;

                case 'sl' :
                    $tarefa = 'Saco de Lixo';
                    break;

                case 'lq' :
                    $tarefa = 'Limpeza de Quarta';
                    break;

                case 'll' :
                    $tarefa = 'Lavar Louça';
                    break;

                case 'aq' :
                    $tarefa = 'Arrumar Quarto';
                    break;

                case 'ls' :
                    $tarefa = 'Limpeza de Segunda';
                    break;

                case 'gl' :
                    $tarefa = 'Guardar Louças';
                    break;
            }

            $itens .= View::render('pages/realizadas/item', [
                'nome' => $filhoAtivo,
                'tarefa' => $tarefa,
                'data' => date('d/m/Y H:i:s', strtotime($obTarefa->dt_tarefa)),
                'codigo' => $obTarefa->id
            ]);
        }

        //RETORNA OS FEEDBACKS
        return $itens;

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
                return Alert::getSuccess('Tarefa Registrada com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Tarefa excluida com sucesso!');
                break;
            case 'close':
                return Alert::getError('Ooops! Para remover essa tarefa, primeiro exclua o fechamento correspondente. :(');
                break;
            case 'duplicated':
                return Alert::getError('Ooops! Você já registrou essa tarefa hoje. :(');
                break;
        }

    }

    /**
     * Método responsável por retornar o conteúdo (view) da depoimentos
     * @param Request $request
     */
    public static function getTarefa($request,$id)
    {
        //VARIAVEIS PARA CARREGAR OS DADOS NA PÁGINA
        $filhoAtivo = $id;
        $botaoA = '';
        $botaoB = '';
        $filho = '';

        //VALIDA FILHO
        if($filhoAtivo == 0){

            //VIEW DA HOME
            $content = View::render('pages/realizadas',[
                'itens' => self::getTarefasItems($request,$id),
                'botaoA' => '',
                'botaoB' => '',
                'status' => self::getStatus($request)

            ]);

            //RETORNA A VIEW DA PÁGINA
            return parent::getPage('REALIZADAS > Home Tasks', $content);

        }

        //DEFINE O FILHO PARA EXIBIÇÃO DO FECHAMENTO
        switch($filhoAtivo) {

            case 1 :
                $filho = 'Alice';
                $botaoA = 'primary';
                break;

            case 2 :
                $filho = 'Arthur';
                $botaoB = 'primary';
                break;

        }

        //VIEW DA HOME
        $content = View::render('pages/realizadas',[
            'itens' => self::getTarefasItems($request,$id),
            'botaoA' => $botaoA,
            'botaoB' => $botaoB,
            'status' => self::getStatus($request)

        ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('REALIZADAS > Home Tasks', $content);
    }

    /**
     * Cadastra um feedback
     * @param Request $request
     * @return string
     */
    public static function insertTarefa($request): string
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();


        //NOVA INSTÂNCIA DE FEEDBACK
        $obTarefa = new EntityTarefas;
        $obTarefa->cdfilho = $postVars['nome'];
        $obTarefa->cdtarefa = $postVars['tarefa'];
        $obTarefa->cadastrar();

        //RETORNA A PAGINA DE LISTAGEM DE FEEDBACKS
        $request->getRouter()->redirect('/realizadas/' . $postVars['nome'] . '/edit?status=created');

    }

    /**
     * Exclui feedback do DB
     * @param Request $request
     */
    public static function setDeleteTarefa($request)
    {

        //POST VARS
        $posVars = $request->getPostVars();

        //OBTEM TAREFA DO DB
        $obtarefa = EntityTarefas::getTarefasById($posVars['id']);

        $ano = substr($obtarefa->dt_tarefa,0,4);
        $mes = substr($obtarefa->dt_tarefa,5,2);

        $results = EntityFechamento::getFechamento('cdfilho = '.$obtarefa->cdfilho. ' and Month(data) = '.$mes.' and Year(data) = '.$ano);
        $obFechamento = $results->fetchObject(EntityFechamento::class);

        if(!empty($obFechamento->id)){
            $request->getRouter()->redirect('/realizadas/' . $obFechamento->cdfilho. '/edit?status=close');
        }

        //ATUALIZA A INSTANCIA
        $obtarefa->id = $posVars['id'] ?? $obtarefa->id;
        $obtarefa->deletar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/realizadas?status=deleted');

    }



}