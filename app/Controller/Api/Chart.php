<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\Chart as EntityChart;
use WilliamCosta\DatabaseManager\Database;
use WilliamCosta\DatabaseManager\Pagination;



class Chart extends Api
{

    public EntityChart $idFilter;

    /**
     * obtem a renderização dos dados para o gráfico
     * @param Request $request
     * @return mixed
     */
    private static function getChartItems($request)
    {
        //DADOS DO GRÁFICO
        $itens = [];

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //SELECIONA O FILTRO COM BASE NA PÁGINA
        $results = EntityChart::getCharts($paginaAtual);

        //RENDERIZA O ITEM
        while($obChart = $results->fetchObject(EntityChart::class)) {

            $itens[] =  [
                'total' => (int)$obChart->total,
                'edicao' => $obChart->edicao
            ];

        }
        //RETORNA OS FEEDBACKS
        return $itens;

    }

    /**
     * Retorna os feedbacks cadastrados
     * @param Request $request
     * @return array
     */
    public static function getCharts($request): array
    {
        return [
            'results' => self::getChartItems($request)
        ];
        
    }

    /**
     * Retorna o feedback pela ID
     * @param integer $idFilter
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public static function getChart($request,$idFilter)
    {
        //FEEDBACKS
        $itens = [];

        //VALIDA SE O ID É INT
        if(!is_numeric($idFilter)){
            throw new \Exception("O id '" .$idFilter. "' não é válido",400);
        }

        //DEFINE O FILTRO DO GRÁFICO
        $results = EntityChart::getCharts($idFilter);

        $obChart = $results->fetchObject(EntityChart::class);

//        echo "<pre>";
//        print_r($results);
//        echo "<pre>";
//        exit;

        //VALIDA SE FEEDBACK EXISTE
        if(!$obChart instanceof EntityChart){
            throw new \Exception("O depoimento " .$idFilter. " não foi encontrado",404);
        }

        //RENDERIZA O ITEM
        while($obChart = $results->fetchObject(EntityChart::class)) {

            $itens[] = [
                'total' => (int)$obChart->total,
                'edicao' => $obChart->edicao
            ];
        }





        foreach ($itens as $key => $value) {

            $itenss = $value;


            //RETORNA OS FEEDBACKS
            return $itens;
        }


    }

    /**
     * Cadastra um novo feedback
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public static function setNewFeedback($request){
        // POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['nome']) or !isset($postVars['mensagem'])){
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios",40 );
        }

        //NOVO FEEBACK
        $obFeedback = new EntityFeedback;
        $obFeedback->nome = $postVars['nome'];
        $obFeedback->mensagem = $postVars['mensagem'];
        $obFeedback->cadastrar();

        //RETORNA OS DETALHES DO FEEDBACK CADASTRADO
        return [
            'id' => (int)$obFeedback->id,
            'nome' => $obFeedback->nome,
            'mensagem' => $obFeedback->mensagem,
            'data' => $obFeedback->data
        ];

    }

    /**
     * Atualiza um  feedback
     * @param Request $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public static function setEditFeedback($request, $id){
        // POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['nome']) or !isset($postVars['mensagem'])){
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios",40 );
        }

        //BUSCA O FEEDBACK NO BANCO
        $obFeedback = EntityFeedback::getFeedbackById($id);

        //VALIDA A INSTANCIA
        if(!$obFeedback instanceof EntityFeedback){
            throw new \Exception("O depoimento " .$id. " não foi encontrado",404);
        }

        //ATUALIZA FEEBACK
        $obFeedback->nome = $postVars['nome'];
        $obFeedback->mensagem = $postVars['mensagem'];
        $obFeedback->atualizar();

        //RETORNA OS DETALHES DO FEEDBACK ATUALIZADO
        return [
            'id' => (int)$obFeedback->id,
            'nome' => $obFeedback->nome,
            'mensagem' => $obFeedback->mensagem,
            'data' => $obFeedback->data
        ];

    }

    /**
     * Exclui um  feedback
     * @param Request $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public static function setDeleteFeedback($request, $id): array
    {
        //BUSCA O FEEDBACK NO BANCO
        $obFeedback = EntityFeedback::getFeedbackById($id);

        //VALIDA A INSTANCIA
        if(!$obFeedback instanceof EntityFeedback){
            throw new \Exception("O depoimento " .$id. " não foi encontrado",404);
        }

        //EXCLUI FEEBACK
        $obFeedback->deletar();

        //RETORNA O SUCESSO DA EXCLUSÃO
        return [
            'sucesso' => true
        ];

    }


}