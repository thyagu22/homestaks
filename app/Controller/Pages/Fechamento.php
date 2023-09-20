<?php

namespace App\Controller\Pages;

use App\Model\Entity\Feedback as EntityFeedback;
use App\Model\Entity\Tarefas as EntityTarefas;
use \App\Utils\View;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Fechamento as EntityFechamento;
use App\Http\Request;
use WilliamCosta\DatabaseManager\Pagination;
use PDOStatement;
use App\Controller\Pages\Alert;

class Fechamento extends Page {

    /**
     * Método responsável por retornar o conteúdo (view) da home
     * @param Request $request
     * @param int $id
     * @return string
     */
    public static function getFechamento(Request $request, int $id): string
    {
        //VARIAVEIS PARA CARREGAR OS DADOS NA PÁGINA
        $filhoAtivo = $id;
        $botaoA = '';
        $botaoB = '';

        //VALIDA FILHO
        if($filhoAtivo == 0){

            //VIEW DA HOME
            $content = View::render('pages/fechamento',[
                'itens' => self::getFechamentoFilho($request,$id),
                'totalM' => 0,
                'botaoA' => '',
                'botaoB' => '',
                'filho' => '',
                'cdfilho' => $id,
                'status' => self::getStatus($request)

            ]);

            //RETORNA A VIEW DA PÁGINA
            return parent::getPage('FECHAMENTO > Home Tasks', $content);

        }


        //CALCULA O FECHAMENTO
        $mes = date("m");
        $ano = '20'.date("y");
        $tipo = 'mensal';

        $results = EntityFechamento::getFechamentoById($id,$mes,$ano,$tipo);
        $obFechamento = $results->fetchObject(EntityTarefas::class);



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
        $content = View::render('pages/fechamento',[
            'itens' => self::getFechamentoFilho($request,$id),
            'totalM' => number_format($obFechamento->totalm ?? 0,2),
            'botaoA' => $botaoA,
            'botaoB' => $botaoB,
            'filho' => $filho,
            'cdfilho' => $id,
            'status' => self::getStatus($request)

        ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('FECHAMENTO > Home Tasks', $content);
    }

    /**
     *
     * @param Request $request
     * @return string
     */
public static function getFechamentoFilho($request, $id){
    $itens = '';

    $mes = date("m");
    $ano = '20'.date("y");

    //$obNewFechamento
      $results  = EntityFechamento::getTarefas($id,$mes,$ano);
      while($obNewFechamento = $results->fetchObject(EntityFechamento::class)){
          switch($obNewFechamento->cdfilho) {

              case 1 :
                  $filhoAtivo = 'Alice';
                  break;

              case 2 :
                  $filhoAtivo = 'Arthur';
                  break;
          }

          switch($obNewFechamento->cdtarefa) {

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
          $itens .= View::render('pages/fechamento/item', [
              'tarefa' => $tarefa,
              'total' => $obNewFechamento->qtd

          ]);
      }
    //RETORNA OS FEEDBACKS
    return $itens;
}

    /**
     * Cadastra um fechamento
     * @param Request $request
     */
    public static function insertFechamento($request)
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();

        //INICIA VARIÁVEIS

        $mes = date("m");
        $ano = '20'.date("y");
        $tipo = 'tarefa';
        $qtdsl = 0;
        $qtdgr = 0;
        $qtdaq = 0;
        $qtdlc = 0;
        $qtdfs = 0;
        $qtdls = 0;
        $qtdlq = 0;
        $qtdll = 0;
        $qtdgl = 0;

        //VALIDA SE EXISTE FECHAMENTO PARA O MÊS
        $fechamento = EntityFechamento::getFechamentoMes($postVars['filho'],$mes,$ano);

        if($fechamento->qtd > 0) {
            $request->getRouter()->redirect('/fechamentos/' . $postVars['filho'] . '/edit?status=duplicated');
        }


        //BUSCA NO BANCO DADOS PARA FECHAMENTO
        $results = EntityFechamento::getFechamentoById($postVars['filho'],$mes,$ano,$tipo);
        while($obFechamentoTarefa = $results->fetchObject(EntityTarefas::class)) {

            switch($obFechamentoTarefa->cdtarefa) {

                case 'gr' :
                    $qtdgr = $obFechamentoTarefa->totalt;
                    break;

                case 'fs' :
                    $qtdfs = $obFechamentoTarefa->totalt;
                    break;

                case 'lc' :
                    $qtdlc = $obFechamentoTarefa->totalt;
                    break;

                case 'sl' :
                    $qtdsl = $obFechamentoTarefa->totalt;
                    break;

                case 'lq' :
                    $qtdlq = $obFechamentoTarefa->totalt;
                    break;

                case 'll' :
                    $qtdll = $obFechamentoTarefa->totalt;
                    break;

                case 'aq' :
                    $qtdaq = $obFechamentoTarefa->totalt;
                    break;

                case 'ls' :
                    $qtdls = $obFechamentoTarefa->totalt;
                    break;

                case 'gl' :
                    $qtdgl = $obFechamentoTarefa->totalt;
                    break;
            }

        }

        //NOVA INSTÂNCIA DE FECHAMENTO
        $obFechamento = new EntityFechamento();
        $obFechamento->cdfilho = $postVars['filho'];
        $obFechamento->totalfc = $postVars['totalm'];
        $obFechamento->qtdaq = ($qtdaq) ?: 0;
        $obFechamento->qtdfs = ($qtdfs) ?: 0;
        $obFechamento->qtdgl = ($qtdgl) ?: 0;
        $obFechamento->qtdlc = ($qtdlc) ?: 0;
        $obFechamento->qtdls = ($qtdls) ?: 0;
        $obFechamento->qtdlq = ($qtdlq) ?: 0;
        $obFechamento->qtdgr = ($qtdgr) ?: 0;
        $obFechamento->qtdll = ($qtdll) ?: 0;
        $obFechamento->qtdsl = ($qtdsl) ?: 0;
        $obFechamento->cadastrar();

        //RETORNA A PAGINA DE LISTAGEM DE FEEDBACKS
        $request->getRouter()->redirect('/fechamentos/' . $obFechamento->cdfilho . '/edit?status=created');

    }

    public static function getFechamentosItens($request,$id)
    {
        $itens = '';

        $ano = '20'.date("y");

        //$obNewFechamento
        $results  = EntityFechamento::getFechamentos($id,$ano);
        while($obFechamentos = $results->fetchObject(EntityFechamento::class)){
            switch($obFechamentos->cdfilho) {

                case 1 :
                    $filhoAtivo = 'Alice';
                    break;

                case 2 :
                    $filhoAtivo = 'Arthur';
                    break;
            }

            $total = $obFechamentos->totalfc;

            $itens .= View::render('pages/fechamento/itens', [
                'codigo' => $obFechamentos->id,
                'filho' => $filhoAtivo,
                'total' => number_format($total,2),
                'data' => date('d/m/Y H:i:s', strtotime($obFechamentos->data)),
                'status' => self::getStatus($request)


            ]);
        }
        //RETORNA OS FEEDBACKS
        return $itens;


    }

    public static function getFechamentos(Request $request, int $id): string
    {
        //VARIAVEIS PARA CARREGAR OS DADOS NA PÁGINA
        $filhoAtivo = $id;
        $botaoA = '';
        $botaoB = '';

        //VALIDA FILHO
        if($filhoAtivo == 0){

            //VIEW DA HOME
            $content = View::render('pages/fechamentos',[
                'itens' => self::getFechamentosItens($request,'(1,2)'),
                'botaoA' => '',
                'botaoB' => '',
                'filhoA' => '',
                'status' => self::getStatus($request)

            ]);

            //RETORNA A VIEW DA PÁGINA
            return parent::getPage('FECHAMENTO > Home Tasks', $content);

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
            $content = View::render('pages/fechamentos',[
                'itens' => self::getFechamentosItens($request,$id),
                'botaoA' => $botaoA,
                'botaoB' => $botaoB,
                'filhoA' => $filho,
                'cdfilho' => $id,
                'status' => self::getStatus($request)

            ]);

            //RETORNA A VIEW DA PÁGINA
            return parent::getPage('FECHAMENTOS > Home Tasks', $content);
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
                return Alert::getSuccess('Fechamento Realizado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Fechamento excluido com sucesso!');
                break;
            case 'notek':
                return Alert::getError('Ooops! Por favor utilize um e-mail @teknisa.');
                break;
            case 'duplicated':
                return Alert::getError('Ooops! Você já realizou o fechamento desse mês. :(');
                break;
        }

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
     * Exclui feedback do DBs
     * @param Request $request
     * @param integer $id
     */
    public static function setDeleteFechamento($request)
    {
        //POST VARS
        $posVars = $request->getPostVars();

        //OBTEM FEEDBACK DO DB
        $results = EntityFechamento::getFechamento('id = '.$posVars['id']);
        $obFechamentos = $results->fetchObject(EntityFechamento::class);

        //VALIDA A INSTANCIA
        if(!$obFechamentos instanceof EntityFechamento) {
            $request->getRouter()->redirect('/fechamentos');
        }


        //ATUALIZA A INSTANCIA
        $obFechamentos->id = $posVars['id'] ?? $obFechamentos->id;
        $obFechamentos->deletar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/fechamentos?status=deleted');

    }

    }
