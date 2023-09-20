<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\Feedback as EntityFeedback;
use WilliamCosta\DatabaseManager\Database;
use WilliamCosta\DatabaseManager\Pagination;

class Feedback extends Api
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
        $itens = [];

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

            $itens[] =  [
                'id' => (int)$obFeedback->id,
                'nome' => $obFeedback->nome,
                'mensagem' => $obFeedback->mensagem,
                'data' => $obFeedback->data
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
    public static function getFeedbacks($request): array
    {
        return [
            'feedbacks' => self::getFeedbackItems($request,$obPagination),
            'paginacao' => parent::getPagination($request,$obPagination)
        ];
        
    }

    /**
     * Retorna o feedback pela ID
     * @param integer $id
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public static function getFeedback($request,$id)
    {
        //VALIDA SE O ID É INT
        if(!is_numeric($id)){
            throw new \Exception("O id '" .$id. "' não é válido",400);
        }
        //BUSCA FEEDBACK
        $obFeedback = EntityFeedback::getFeedbackById($id);

        //VALIDA SE FEEDBACK EXISTE
        if(!$obFeedback instanceof EntityFeedback){
            throw new \Exception("O depoimento " .$id. " não foi encontrado",404);
        }

        //RETORNA OS DETALHES DO FEEDBACK
        return [
            'id' => (int)$obFeedback->id,
            'nome' => $obFeedback->nome,
            'mensagem' => $obFeedback->mensagem,
            'data' => $obFeedback->data
        ];

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